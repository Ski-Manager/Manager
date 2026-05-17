<?php
/**
 * Ski Manager Ops — All-in-one admin dashboard
 */
define('ADMIN_PASSWORD', 'Bordeaux147');
define('SESSION_NAME',   'skiman_ops');
define('SESSION_LIFE',   3600 * 8);

session_name(SESSION_NAME);
session_set_cookie_params(['lifetime'=>SESSION_LIFE,'path'=>'/','secure'=>true,'httponly'=>true,'samesite'=>'Strict']);
session_start();

$error    = '';
$loggedIn = !empty($_SESSION['ops_auth']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) { $_SESSION['ops_auth'] = true; header('Location: '.$_SERVER['REQUEST_URI']); exit; }
    $error = 'Invalid password.';
}
if (isset($_GET['logout'])) { session_destroy(); header('Location: '.strtok($_SERVER['REQUEST_URI'],'?')); exit; }

// ── DEPLOY STREAM ─────────────────────────────────────────────────────────────
// PHP runs inside a Docker container and cannot reach /root/ on the host.
// Proxy the request to ski-deploy-api.py (runs as root on the host at port 9876).
define('DEPLOY_API', 'http://172.20.0.1:9876');
define('DEPLOY_SECRET', 'sm-deploy-9k2x');

if ($loggedIn && isset($_GET['deploy'])) {
    $target  = $_GET['deploy']  ?? '';
    $confirm = $_GET['confirm'] ?? '0';

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no');
    if (ob_get_level()) { ob_end_clean(); }

    $url = DEPLOY_API . '/deploy?target=' . urlencode($target)
         . '&confirm=' . urlencode($confirm)
         . '&secret='  . urlencode(DEPLOY_SECRET);

    set_time_limit(300);
    ignore_user_abort(true);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_TIMEOUT        => 300,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_WRITEFUNCTION  => function($ch, $data) {
            echo $data;
            flush();
            return strlen($data);
        },
    ]);
    curl_exec($ch);
    $errno = curl_errno($ch);
    curl_close($ch);

    if ($errno) {
        echo "data: " . json_encode([
            'err' => 'Deploy API unreachable (errno ' . $errno . '). Is ski-deploy-api.service running on the host?'
        ]) . "\n\n";
        flush();
    }
    exit;
}

// ── API ───────────────────────────────────────────────────────────────────────
define('DOCKER_API','http://172.20.0.1:2375');

function dkGet($path){
    $ch=curl_init(DOCKER_API.$path);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>8]);
    $r=curl_exec($ch);curl_close($ch);
    return json_decode($r??'null',true);
}
function dkPost($path,$body='',$timeout=30){
    $ch=curl_init(DOCKER_API.$path);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,
        CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>$timeout,
        CURLOPT_HTTPHEADER=>['Content-Type: application/json']]);
    $r=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
    return[$code,json_decode($r??'null',true)??$r];
}
function dkDelete($path){
    $ch=curl_init(DOCKER_API.$path);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CUSTOMREQUEST=>'DELETE',CURLOPT_TIMEOUT=>10]);
    curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
    return $code;
}

if ($loggedIn && isset($_GET['api'])) {
    header('Content-Type: application/json');
    header('Cache-Control: no-store');
    switch ($_GET['api']) {

        case 'stats':
            function rCPU(){ $f=fopen('/proc/stat','r');$l=fgets($f);fclose($f);$p=preg_split('/\s+/',trim($l));return[(int)array_sum(array_slice($p,1)),(int)$p[4]]; }
            [$t1,$i1]=rCPU(); usleep(300000); [$t2,$i2]=rCPU();
            $dt=$t2-$t1; $cpu=$dt>0?round(($dt-($i2-$i1))/$dt*100):0;
            $mi=[]; foreach(file('/proc/meminfo')as$l){[$k,$v]=explode(':',$l,2);$mi[trim($k)]=(int)$v;}
            $mT=$mi['MemTotal']??0; $mU=$mT-($mi['MemAvailable']??0);
            $dT=disk_total_space('/'); $dF=disk_free_space('/'); $dU=$dT-$dF;
            $la=explode(' ',file_get_contents('/proc/loadavg'));
            $us=(int)explode(' ',file_get_contents('/proc/uptime'))[0];
            $d=floor($us/86400); $h=floor(($us%86400)/3600); $m=floor(($us%3600)/60);
            $up=$d>0?"{$d}d {$h}h":($h>0?"{$h}h {$m}m":"{$m}m");
            echo json_encode([
                'cpu'   =>$cpu,
                'mem'   =>['used'=>round($mU/1048576,1),'total'=>round($mT/1048576,1),'pct'=>$mT>0?round($mU/$mT*100):0],
                'disk'  =>['used'=>round($dU/1073741824,1),'total'=>round($dT/1073741824,1),'pct'=>$dT>0?round($dU/$dT*100):0],
                'load'  =>$la[0]??'?',
                'uptime'=>$up,
            ]);
            break;

        case 'services':
            $svcs=[
                ['name'=>'CloudPanel','url'=>'https://172.20.0.1:8443','ext'=>'https://panel.ski-manager.cloud',   'icon'=>'🖥️'],
                ['name'=>'WhoDB',     'url'=>'http://172.20.0.1:8080', 'ext'=>'https://whodb.ski-manager.cloud',   'icon'=>'🗄️'],
                ['name'=>'XyOps',    'url'=>'http://172.20.0.1:5522', 'ext'=>'https://xyops.ski-manager.cloud',   'icon'=>'📊'],
                ['name'=>'Terminal', 'url'=>'http://172.20.0.1:7681', 'ext'=>'https://terminal.ski-manager.cloud','icon'=>'💻'],
                ['name'=>'Listmonk', 'url'=>'http://172.20.0.1:9010', 'ext'=>'',                                  'icon'=>'📧'],
                ['name'=>'Portainer','url'=>'http://172.20.0.1:9443', 'ext'=>'',                                  'icon'=>'🐳'],
                ['name'=>'FileRise', 'url'=>'http://172.20.0.1:3000', 'ext'=>'',                                  'icon'=>'📁'],
                ['name'=>'1Panel',   'url'=>'http://172.20.0.1:16334','ext'=>'',                                  'icon'=>'⚙️'],
            ];
            $mh=curl_multi_init(); $chs=[];
            foreach($svcs as $i=>&$s){
                $ch=curl_init($s['url']);
                curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>2,
                    CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_SSL_VERIFYHOST=>false,CURLOPT_NOBODY=>true]);
                curl_multi_add_handle($mh,$ch); $chs[$i]=$ch; $s['t0']=microtime(true);
            }
            do{curl_multi_exec($mh,$run);curl_multi_select($mh);}while($run>0);
            foreach($chs as $i=>$ch){
                $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
                $svcs[$i]['ms']=round((microtime(true)-$svcs[$i]['t0'])*1000);
                $svcs[$i]['up']=$code>0&&$code<500;
                unset($svcs[$i]['url'],$svcs[$i]['t0']);
                curl_multi_remove_handle($mh,$ch); curl_close($ch);
            }
            curl_multi_close($mh);
            echo json_encode(array_values($svcs)); break;

        case 'docker':
            $raw=dkGet('/v1.43/containers/json?all=1');
            $out=[];
            foreach(($raw??[])as$c){
                $nm=ltrim($c['Names'][0]??'','/');
                $state=$c['State']??'';
                $st=$c['Status']??'';
                $short=preg_replace('/^Up (\d+) (\w)\w+/','Up $1$2',$st);
                $short=preg_replace('/^Exited \(\d+\).*/','Exited',$short);
                $img=preg_replace('/:.*/','',preg_replace('/^.*\//','',($c['Image']??'')));
                $out[]=['name'=>$nm,'status'=>$short,'state'=>$state,'image'=>$img,
                        'full_image'=>$c['Image']??'','up'=>$state==='running'];
            }
            echo json_encode($out); break;

        case 'docker_action':
            $name=preg_replace('/[^a-zA-Z0-9_\-]/','',($_POST['name']??''));
            $act=in_array(($_POST['action']??''),['restart','stop','start'])?$_POST['action']:'';
            if(!$name||!$act){ echo json_encode(['ok'=>false,'out'=>'bad request']); break; }
            [$code]=dkPost('/v1.43/containers/'.urlencode($name).'/'.$act,'',15);
            echo json_encode(['ok'=>($code>=200&&$code<300),'out'=>'HTTP '.$code]);
            break;

        case 'update':
            set_time_limit(300); ignore_user_abort(true);
            $name=preg_replace('/[^a-zA-Z0-9_\-]/','',($_POST['name']??''));
            if(!$name){ echo json_encode(['ok'=>false,'log'=>'Invalid name']); break; }
            $log=[];
            $log[]="» Inspecting container: $name";

            // 1. Inspect
            $info=dkGet('/v1.43/containers/'.urlencode($name).'/json');
            if(!$info||isset($info['message'])){ echo json_encode(['ok'=>false,'log'=>$info['message']??'not found']); break; }

            $image=$info['Config']['Image'];
            $log[]="  Image: $image";

            // 2. Get current image digest
            $oldImg=dkGet('/v1.43/images/'.urlencode($image).'/json');
            $oldId=substr($oldImg['Id']??'',7,12);
            $log[]="  Current ID: $oldId";

            // 3. Pull latest
            $log[]="» Pulling $image …";
            $parts=explode(':',$image,2); $imgName=$parts[0]; $imgTag=$parts[1]??'latest';
            $ch=curl_init(DOCKER_API.'/v1.43/images/create?fromImage='.urlencode($imgName).'&tag='.urlencode($imgTag));
            curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>'',CURLOPT_TIMEOUT=>300]);
            $pullOut=curl_exec($ch); $pullCode=curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
            // Parse last line of ndjson pull output for status
            $pullLines=array_filter(array_map('trim',explode("\n",trim($pullOut??''))));
            $lastPull=json_decode(end($pullLines)??'{}',true);
            $log[]="  Status: ".($lastPull['status']??("HTTP $pullCode"));

            // 4. Check new image ID
            $newImg=dkGet('/v1.43/images/'.urlencode($image).'/json');
            $newId=substr($newImg['Id']??'',7,12);
            $log[]="  New ID:  $newId";

            if($oldId&&$newId&&$oldId===$newId){
                $log[]="✓ Already up to date — no restart needed.";
                echo json_encode(['ok'=>true,'updated'=>false,'log'=>implode("\n",$log)]); break;
            }
            $log[]="  New version detected!";

            // 5. Build recreate config from inspect
            $nets=[];
            foreach(($info['NetworkSettings']['Networks']??[]) as $netName=>$netCfg){
                $aliases=array_values(array_filter($netCfg['Aliases']??[],fn($a)=>$a!==$name&&$a!==substr($info['Id']??'',0,12)));
                $nets[$netName]=['Aliases'=>$aliases];
            }
            $hc=$info['HostConfig'];
            $createBody=json_encode([
                'Hostname'     =>$info['Config']['Hostname']??'',
                'Image'        =>$image,
                'Env'          =>$info['Config']['Env']??[],
                'Cmd'          =>$info['Config']['Cmd'],
                'Entrypoint'   =>$info['Config']['Entrypoint'],
                'ExposedPorts' =>(object)array_map(fn($_)=>new stdClass(),$info['Config']['ExposedPorts']??[]),
                'Labels'       =>(object)($info['Config']['Labels']??[]),
                'HostConfig'   =>[
                    'Binds'         =>$hc['Binds']??[],
                    'Mounts'        =>array_filter(array_map(fn($m)=>array_filter(['Type'=>$m['Type'],'Source'=>$m['Source']??'','Target'=>$m['Destination']??'','ReadOnly'=>$m['RW']??true?false:true]),($hc['Mounts']??[]))),
                    'PortBindings'  =>$hc['PortBindings']??new stdClass,
                    'RestartPolicy' =>$hc['RestartPolicy']??['Name'=>'no'],
                    'NetworkMode'   =>$hc['NetworkMode']??'bridge',
                    'Privileged'    =>$hc['Privileged']??false,
                    'CapAdd'        =>$hc['CapAdd'],
                    'CapDrop'       =>$hc['CapDrop'],
                ],
                'NetworkingConfig'=>['EndpointsConfig'=>$nets],
            ]);

            // 6. Stop
            $log[]="» Stopping $name …";
            dkPost('/v1.43/containers/'.urlencode($name).'/stop','',30);

            // 7. Remove
            $log[]="» Removing old container …";
            dkDelete('/v1.43/containers/'.urlencode($name).'?v=false');

            // 8. Create
            $log[]="» Creating new container …";
            [$cCode,$cResp]=dkPost('/v1.43/containers/create?name='.urlencode($name),$createBody,30);
            $log[]="  Create: HTTP $cCode";
            if($cCode<200||$cCode>=300){
                $msg=is_array($cResp)?($cResp['message']??json_encode($cResp)):$cResp;
                $log[]="✗ Failed: $msg";
                echo json_encode(['ok'=>false,'log'=>implode("\n",$log)]); break;
            }
            $newId2=$cResp['Id']??'';

            // 9. Start
            $log[]="» Starting new container …";
            [$sCode]=dkPost('/v1.43/containers/'.urlencode($newId2).'/start','',15);
            $log[]="  Start: HTTP $sCode";

            $ok=$sCode>=200&&$sCode<300;
            $log[]=$ok?"✓ Update complete!":"✗ Start failed (HTTP $sCode)";
            echo json_encode(['ok'=>$ok,'updated'=>true,'log'=>implode("\n",$log)]);
            break;

        case 'logs':
            $name=preg_replace('/[^a-zA-Z0-9_\-]/','',($_POST['name']??''));
            $lines=min(1000,max(10,(int)($_POST['lines']??100)));
            if(!$name){echo json_encode(['ok'=>false,'log'=>'']);break;}
            $ch=curl_init(DOCKER_API.'/v1.43/containers/'.urlencode($name).'/logs?stdout=1&stderr=1&tail='.$lines.'&timestamps=0');
            curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10]);
            $raw=curl_exec($ch)??'';curl_close($ch);
            // Strip Docker multiplexed stream 8-byte frame headers
            $out='';$i=0;$len=strlen($raw);
            while($i+8<=$len){
                $sz=unpack('N',substr($raw,$i+4,4))[1];$i+=8;
                if($i+$sz>$len)break;
                $out.=substr($raw,$i,$sz);$i+=$sz;
            }
            if(!$out)$out=$raw;
            echo json_encode(['ok'=>true,'log'=>$out]);break;

        case 'image_check':
            // Check if a newer image exists without recreating the container
            $name=preg_replace('/[^a-zA-Z0-9_\-]/','',($_POST['name']??''));
            if(!$name){ echo json_encode(['ok'=>false]); break; }
            $info=dkGet('/v1.43/containers/'.urlencode($name).'/json');
            if(!$info){ echo json_encode(['ok'=>false,'msg'=>'not found']); break; }
            $image=$info['Config']['Image'];
            $oldImg=dkGet('/v1.43/images/'.urlencode($image).'/json');
            $oldId=$oldImg['Id']??'';
            // Pull to check
            set_time_limit(120);
            $parts=explode(':',$image,2);
            $ch=curl_init(DOCKER_API.'/v1.43/images/create?fromImage='.urlencode($parts[0]).'&tag='.urlencode($parts[1]??'latest'));
            curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>'',CURLOPT_TIMEOUT=>120]);
            curl_exec($ch); curl_close($ch);
            $newImg=dkGet('/v1.43/images/'.urlencode($image).'/json');
            $newId=$newImg['Id']??'';
            echo json_encode(['ok'=>true,'update_available'=>($oldId&&$newId&&$oldId!==$newId),'image'=>$image]);
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ski Manager — Ops</title>
<style>
:root{
  --bg:#080c10;--bg1:#0c1118;--bg2:#121820;--bg3:#181f2a;
  --bd:#1c2333;--bd2:#243045;
  --tx:#c8d6e5;--tx2:#7a8fa8;--tx3:#3e4e62;
  --blue:#3d8ef8;--blue-a:rgba(61,142,248,.12);
  --green:#2ea04a;--green-a:rgba(46,160,74,.1);--green-t:#3fb950;
  --yellow:#c9971e;--red:#e5484d;--red-a:rgba(229,72,77,.1);
  --purple:#9f7af5;
  --mono:'SF Mono',ui-monospace,Menlo,Consolas,monospace;
  --r:10px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden}
body{font-family:-apple-system,BlinkMacSystemFont,'Inter','Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--tx);display:flex;flex-direction:column;height:100vh}
::-webkit-scrollbar{width:4px;height:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--bd2);border-radius:2px}

/* LOGIN */
.lw{display:flex;align-items:center;justify-content:center;flex:1;background:radial-gradient(ellipse 90% 60% at 50% -20%,rgba(61,142,248,.08) 0%,transparent 70%)}
.lc{background:var(--bg1);border:1px solid var(--bd);border-radius:20px;padding:48px 52px;width:390px;text-align:center;box-shadow:0 40px 80px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.04)}
.lc-ico{font-size:2.6rem;margin-bottom:14px;display:block;filter:drop-shadow(0 0 20px rgba(61,142,248,.45))}
.lc h1{font-size:1.22rem;font-weight:700;color:#e4edf7;margin-bottom:5px;letter-spacing:-.02em}
.lc-sub{font-size:.81rem;color:var(--tx3);margin-bottom:30px}
.lc input{width:100%;padding:12px 16px;background:var(--bg);border:1px solid var(--bd);border-radius:9px;color:#e4edf7;font-size:.94rem;outline:none;transition:border-color .2s,box-shadow .2s}
.lc input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(61,142,248,.14)}
.lc-btn{display:block;width:100%;padding:12px;margin-top:11px;background:linear-gradient(135deg,#1f6feb,#2685f5);border:none;border-radius:9px;color:#fff;font-size:.94rem;font-weight:600;cursor:pointer;transition:opacity .15s,transform .1s;letter-spacing:-.01em}
.lc-btn:hover{opacity:.88;transform:translateY(-1px)}
.lc-err{margin-top:13px;padding:9px 13px;background:var(--red-a);border:1px solid rgba(229,72,77,.35);border-radius:8px;color:var(--red);font-size:.8rem}

/* HEADER */
#hdr{display:flex;align-items:center;padding:0 13px;height:44px;background:var(--bg1);border-bottom:1px solid var(--bd);flex-shrink:0;gap:9px;z-index:10}
.brand{display:flex;align-items:center;gap:8px;white-space:nowrap;text-decoration:none}
.brand-ico{font-size:1.1rem}
.brand-name{font-size:.87rem;font-weight:700;color:#e4edf7;letter-spacing:-.02em;line-height:1.1}
.brand-tag{font-size:.63rem;color:var(--tx3);font-weight:400;display:block}
.vsep{width:1px;height:16px;background:var(--bd);flex-shrink:0}
.tabs{display:flex;background:var(--bg);border:1px solid var(--bd);border-radius:8px;padding:2px;gap:1px}
.tab{padding:3px 10px;border-radius:5px;font-size:.74rem;font-weight:500;cursor:pointer;color:var(--tx3);border:none;background:none;transition:all .15s;user-select:none}
.tab:hover{color:var(--tx)}
.tab.active{background:var(--bg3);color:#e4edf7;box-shadow:0 1px 3px rgba(0,0,0,.35)}
#hdr-r{margin-left:auto;display:flex;align-items:center;gap:7px}
#clock{font-size:.7rem;color:var(--tx3);font-family:var(--mono);letter-spacing:.04em;min-width:58px;text-align:right}
.upchip{font-size:.66rem;padding:2px 7px;border-radius:5px;background:var(--green-a);border:1px solid rgba(46,160,74,.22);color:var(--green-t);font-weight:600;font-family:var(--mono)}
.rdot{width:5px;height:5px;border-radius:50%;background:var(--blue);opacity:0}
.rdot.pulse{animation:rpulse .9s ease-out forwards}
@keyframes rpulse{0%{opacity:.9}100%{opacity:0}}
.lout{font-size:.71rem;color:var(--tx3);text-decoration:none;padding:4px 9px;border:1px solid var(--bd);border-radius:6px;transition:all .15s}
.lout:hover{color:var(--red);border-color:rgba(229,72,77,.4);background:var(--red-a)}

/* STATS */
#stats{display:flex;background:var(--bg1);border-bottom:1px solid var(--bd);flex-shrink:0}
.stat{flex:1;padding:7px 14px;border-right:1px solid var(--bd)}
.stat:last-child{border-right:none}
.slbl{font-size:.56rem;text-transform:uppercase;letter-spacing:.1em;color:var(--tx3);margin-bottom:3px;font-weight:700}
.srow{display:flex;align-items:baseline;gap:6px}
.sval{font-size:.81rem;font-weight:700;color:#e4edf7;font-variant-numeric:tabular-nums}
.ssub{font-size:.61rem;color:var(--tx3)}
.sbar{height:2px;background:var(--bd2);border-radius:1px;margin-top:5px;overflow:hidden}
.sfill{height:100%;border-radius:1px;background:var(--green);transition:width .9s cubic-bezier(.4,0,.2,1)}
.sfill.w{background:var(--yellow)}.sfill.c{background:var(--red)}

/* LAYOUT */
#main{display:flex;flex:1;overflow:hidden}

/* SIDEBAR */
#sb{width:230px;min-width:230px;overflow-y:auto;border-right:1px solid var(--bd);display:flex;flex-direction:column;background:var(--bg)}
.ss{border-bottom:1px solid var(--bd)}
.ss:last-child{border-bottom:none}
.ss-hd{display:flex;align-items:center;justify-content:space-between;padding:8px 11px 5px}
.ss-title{font-size:.58rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--tx3)}
.ss-badge{font-size:.59rem;color:var(--tx3);background:var(--bg2);border:1px solid var(--bd);border-radius:4px;padding:0 5px;font-family:var(--mono)}
.ss-body{padding:0 6px 7px}

/* Service rows */
.sv{display:flex;align-items:center;gap:6px;padding:4px 5px;border-radius:6px;transition:background .1s}
.sv:hover{background:var(--bg2)}
.dot{width:6px;height:6px;border-radius:50%;background:var(--tx3);flex-shrink:0;transition:background .4s}
.dot.up{background:var(--green)}
.dot.dn{background:var(--red);animation:dnpulse 2s infinite}
@keyframes dnpulse{0%,100%{box-shadow:0 0 0 0 rgba(229,72,77,.5)}50%{box-shadow:0 0 0 5px transparent}}
.svn{flex:1;font-size:.74rem;color:var(--tx)}
.svt{font-size:.63rem;color:var(--tx3);font-family:var(--mono)}
.svt.dn{color:var(--red)}
.svx{font-size:.6rem;opacity:0;color:var(--blue);text-decoration:none;padding:1px 4px;border-radius:3px;transition:opacity .1s}
.sv:hover .svx{opacity:1}

/* Container rows */
.dk{display:flex;align-items:center;gap:5px;padding:4px 5px;border-radius:6px}
.dk:hover{background:var(--bg2)}
.dkn{flex:1;font-size:.73rem;color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100px}
.dks{font-size:.6rem;color:var(--tx3);max-width:52px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dka{display:flex;gap:2px;margin-left:auto;opacity:0;transition:opacity .1s}
.dk:hover .dka{opacity:1}
.ab{font-size:.56rem;padding:1px 5px;border-radius:3px;border:1px solid var(--bd);background:transparent;color:var(--tx3);cursor:pointer;line-height:1.5;transition:all .1s}
.ab:hover{background:var(--bg2);color:var(--tx);border-color:var(--bd2)}
.ab.r:hover{border-color:var(--yellow);color:var(--yellow)}
.ab.s:hover{border-color:var(--red);color:var(--red)}
.ab.g:hover{border-color:var(--green);color:var(--green-t)}
.ab.u:hover{border-color:var(--purple);color:var(--purple)}

/* Quick links */
.lgrp{display:grid;grid-template-columns:1fr 1fr;gap:4px;padding:0 6px 7px}
.lk{display:flex;align-items:center;gap:5px;padding:6px 8px;background:var(--bg2);border:1px solid var(--bd);border-radius:7px;text-decoration:none;color:var(--tx);font-size:.7rem;font-weight:500;transition:all .15s}
.lk:hover{border-color:var(--blue);background:rgba(61,142,248,.06);color:#e4edf7;transform:translateY(-1px)}
.lk-ico{font-size:.8rem}

/* VPS info */
.vps-grid{display:grid;grid-template-columns:auto 1fr;gap:3px 10px;font-size:.73rem}
.vg-lbl{color:var(--tx3)}
.vg-val{color:var(--tx);font-family:var(--mono);font-weight:600}

/* CONTENT */
#content{flex:1;overflow:hidden;display:flex;flex-direction:column}
.panel{display:none;flex:1;overflow:hidden}
.panel.active{display:flex;flex-direction:column}

/* TERMINAL */
#p-terminal{background:#060a0e}
.thdr{display:flex;align-items:center;gap:8px;padding:6px 12px;background:var(--bg1);border-bottom:1px solid var(--bd);flex-shrink:0}
.tdots{display:flex;gap:5px}
.td{width:10px;height:10px;border-radius:50%;cursor:pointer;transition:filter .15s}
.td:hover{filter:brightness(1.35)}
.td.r{background:#ff5f57}.td.y{background:#febc2e}.td.g{background:#28c840}
.ttl{font-size:.72rem;color:var(--tx3);font-family:var(--mono)}
.thdr-r{margin-left:auto;display:flex;align-items:center;gap:6px}
.thdr-btn{font-size:.68rem;color:var(--tx3);background:none;border:1px solid var(--bd);border-radius:5px;padding:3px 8px;cursor:pointer;transition:all .15s}
.thdr-btn:hover{color:var(--blue);border-color:rgba(61,142,248,.45)}
#term-frame{flex:1;border:none;width:100%}

/* OVERVIEW */
#p-overview{padding:13px;overflow-y:auto;background:var(--bg);gap:11px;flex-direction:row;align-items:flex-start;flex-wrap:wrap}
.ov-col{display:flex;flex-direction:column;gap:10px;flex:1;min-width:252px}
.card{background:var(--bg1);border:1px solid var(--bd);border-radius:var(--r);overflow:hidden}
.card-hdr{padding:9px 13px;border-bottom:1px solid var(--bd);font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--tx3);display:flex;justify-content:space-between;align-items:center;background:rgba(0,0,0,.15)}
.card-badge{font-size:.62rem;text-transform:none;letter-spacing:0;padding:1px 7px;border-radius:5px;background:var(--bg2);border:1px solid var(--bd);color:var(--tx3);font-weight:500}
.ov-svc{display:flex;align-items:center;gap:9px;padding:8px 10px;border-bottom:1px solid var(--bd);text-decoration:none;color:inherit;transition:background .1s}
.ov-svc:last-child{border-bottom:none}
.ov-svc:hover{background:var(--bg2)}
.ov-ico{font-size:.95rem;width:22px;text-align:center}
.ov-info{flex:1}
.ov-nm{font-size:.78rem;font-weight:600;color:var(--tx)}
.ov-sub{font-size:.64rem;color:var(--tx3);margin-top:1px;font-family:var(--mono)}
.ov-badge{font-size:.62rem;padding:2px 8px;border-radius:5px;font-weight:600;font-family:var(--mono)}
.ov-badge.up{background:var(--green-a);border:1px solid rgba(46,160,74,.28);color:var(--green-t)}
.ov-badge.dn{background:var(--red-a);border:1px solid rgba(229,72,77,.28);color:var(--red)}

/* Docker table */
.dk-table{width:100%;border-collapse:collapse;font-size:.74rem}
.dk-table th{padding:7px 11px;text-align:left;font-size:.59rem;text-transform:uppercase;letter-spacing:.09em;color:var(--tx3);border-bottom:1px solid var(--bd);background:rgba(0,0,0,.15)}
.dk-table td{padding:7px 11px;border-bottom:1px solid var(--bd);vertical-align:middle}
.dk-table tr:last-child td{border-bottom:none}
.dk-table tr:hover td{background:var(--bg2)}
.st-dot{display:inline-flex;align-items:center;gap:5px;font-size:.7rem;color:var(--tx3);font-family:var(--mono)}
.dk-act{font-size:.61rem;padding:2px 7px;border-radius:4px;border:1px solid var(--bd);background:transparent;color:var(--tx3);cursor:pointer;margin-left:3px;transition:all .12s}
.dk-act:hover{background:var(--bg2);color:var(--tx)}
.dk-act.r:hover{border-color:var(--yellow);color:var(--yellow)}
.dk-act.s:hover{border-color:var(--red);color:var(--red)}
.dk-act.g:hover{border-color:var(--green);color:var(--green-t)}
.dk-act.u{border-color:rgba(159,122,245,.3);color:var(--purple)}
.dk-act.u:hover{border-color:var(--purple);background:rgba(159,122,245,.07)}

/* LOGS */
#p-logs{display:flex;flex-direction:column;overflow:hidden;background:var(--bg)}
.log-bar{display:flex;align-items:center;gap:8px;padding:7px 12px;border-bottom:1px solid var(--bd);background:var(--bg1);flex-shrink:0;flex-wrap:wrap}
.log-bar label{font-size:.74rem;color:var(--tx3)}
.log-bar select{background:var(--bg2);border:1px solid var(--bd);border-radius:6px;color:var(--tx);font-size:.77rem;padding:5px 10px;outline:none;cursor:pointer}
.log-bar select:focus{border-color:var(--blue)}
.log-acts{margin-left:auto;display:flex;gap:5px}
.log-btn{font-size:.71rem;padding:4px 10px;border-radius:6px;border:1px solid var(--bd);background:transparent;color:var(--tx3);cursor:pointer;transition:all .15s}
.log-btn:hover{color:var(--blue);border-color:rgba(61,142,248,.5)}
.log-btn.on{color:var(--blue);border-color:rgba(61,142,248,.5);background:rgba(61,142,248,.07)}
#log-out{flex:1;overflow-y:auto;padding:11px 15px;font-family:var(--mono);font-size:.74rem;line-height:1.75;color:var(--tx);white-space:pre-wrap;word-break:break-all}
.log-empty{padding:32px;text-align:center;color:var(--tx3);font-size:.81rem}

/* UPDATE MODAL */
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.84);z-index:2000;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s;backdrop-filter:blur(3px)}
.overlay.open{opacity:1;pointer-events:all}
.modal{background:var(--bg1);border:1px solid var(--bd2);border-radius:14px;width:560px;max-width:94vw;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,.7);transform:translateY(6px) scale(.98);transition:transform .2s}
.overlay.open .modal{transform:none}
.mhdr{padding:13px 16px;border-bottom:1px solid var(--bd);display:flex;align-items:flex-start;gap:10px}
.mhdr-ico{font-size:1.25rem;margin-top:2px;flex-shrink:0}
.mhdr-text{flex:1}
.mhdr-title{font-size:.87rem;font-weight:700;color:#e4edf7}
.mhdr-sub{font-size:.67rem;color:var(--tx3);font-family:var(--mono);margin-top:2px}
.mbtn-close{background:none;border:1px solid var(--bd);border-radius:6px;color:var(--tx3);cursor:pointer;font-size:.73rem;padding:4px 8px;line-height:1.3;transition:all .15s}
.mbtn-close:hover{color:var(--red);border-color:rgba(229,72,77,.5)}
.mbody{padding:14px 16px}
.mlog{background:var(--bg);border:1px solid var(--bd);border-radius:8px;padding:12px 14px;font-family:var(--mono);font-size:.72rem;color:var(--tx);overflow-y:auto;min-height:140px;max-height:270px;white-space:pre-wrap;line-height:1.75}
.mlog .ok{color:var(--green-t)}.mlog .er{color:var(--red)}.mlog .dim{color:var(--tx3)}
.mfoot{padding:10px 16px;border-top:1px solid var(--bd);display:flex;align-items:center;gap:8px}
.mstatus{flex:1;font-size:.75rem}
.mbtn{padding:7px 15px;border-radius:7px;font-size:.78rem;font-weight:600;cursor:pointer;border:1px solid;transition:all .15s}
.mbtn-upd{background:linear-gradient(135deg,var(--green),#38b557);border-color:var(--green);color:#fff}
.mbtn-upd:hover:not(:disabled){opacity:.87}
.mbtn-upd:disabled{opacity:.38;cursor:not-allowed}
.mbtn-sec{background:transparent;border-color:var(--bd);color:var(--tx3)}
.mbtn-sec:hover{border-color:var(--blue);color:var(--blue)}
.spin{display:inline-block;width:11px;height:11px;border:2px solid var(--bd2);border-top-color:var(--blue);border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle;margin-right:5px}
@keyframes spin{to{transform:rotate(360deg)}}
.bk-ok{padding:2px 9px;border-radius:5px;font-size:.69rem;font-weight:700;background:var(--green-a);border:1px solid rgba(46,160,74,.3);color:var(--green-t)}
.bk-er{padding:2px 9px;border-radius:5px;font-size:.69rem;font-weight:700;background:var(--red-a);border:1px solid rgba(229,72,77,.3);color:var(--red)}
.bk-same{padding:2px 9px;border-radius:5px;font-size:.69rem;font-weight:700;background:var(--bg2);border:1px solid var(--bd);color:var(--tx3)}

/* TOAST */
#toast-stack{position:fixed;bottom:15px;right:15px;display:flex;flex-direction:column;gap:5px;z-index:9999;pointer-events:none}
.toast{padding:8px 13px;background:var(--bg2);border:1px solid var(--bd2);border-radius:9px;font-size:.76rem;color:var(--tx);opacity:0;transform:translateY(8px);transition:opacity .2s,transform .2s;display:flex;align-items:center;gap:7px;max-width:270px}
.toast::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0;background:var(--tx3)}
.toast.ok::before{background:var(--green-t)}.toast.er::before{background:var(--red)}
.toast.ok{border-color:rgba(46,160,74,.3)}.toast.er{border-color:rgba(229,72,77,.3)}
.toast.in{opacity:1;transform:none}
/* DEPLOY */
.dep-btns{display:flex;flex-direction:column;gap:4px;padding:0 8px 8px}
.dep-btn{display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:8px;font-size:.79rem;font-weight:600;cursor:pointer;border:1px solid;text-align:left;transition:all .15s;width:100%;line-height:1.2}
.dep-btn:active{transform:scale(.98)}
.dep-btn:disabled{opacity:.4;cursor:not-allowed}
.dep-btn .dep-ico{font-size:1rem;flex-shrink:0}
.dep-btn .dep-lbl{flex:1}
.dep-btn .dep-env{font-size:.64rem;font-weight:400;opacity:.75;font-family:var(--mono);margin-top:1px}
.dep-vercel{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.12);color:#e4edf7}
.dep-vercel:hover:not(:disabled){background:rgba(255,255,255,.09);border-color:rgba(255,255,255,.28)}
.dep-beta{background:rgba(77,166,255,.06);border-color:rgba(77,166,255,.22);color:var(--blue)}
.dep-beta:hover:not(:disabled){background:rgba(77,166,255,.13)}
.dep-prod{background:rgba(229,72,77,.06);border-color:rgba(229,72,77,.22);color:var(--red)}
.dep-prod:hover:not(:disabled){background:rgba(229,72,77,.12)}
/* Deploy modal */
.dlog{font-family:var(--mono);font-size:.72rem;background:var(--bg);border:1px solid var(--bd);border-radius:8px;padding:10px 12px;min-height:180px;max-height:380px;overflow-y:auto;line-height:1.6;white-space:pre-wrap;word-break:break-all}
.dlog .dl-info{color:var(--tx3)}.dlog .dl-out{color:#c8d9ee}.dlog .dl-err{color:#f0a070}
.dlog .dl-ok{color:var(--green-t);font-weight:700}.dlog .dl-fail{color:var(--red);font-weight:700}
.dep-confirm{display:flex;align-items:flex-start;gap:9px;padding:9px 12px;background:rgba(229,72,77,.07);border:1px solid rgba(229,72,77,.22);border-radius:8px;font-size:.78rem;color:#e4a0a0;margin-bottom:2px}
.dep-confirm input{margin-top:2px;flex-shrink:0;accent-color:var(--red)}
</style>
</head>
<body>

<?php if (!$loggedIn): ?>
<div class="lw">
  <div class="lc">
    <span class="lc-ico">&#x26F7;&#xFE0F;</span>
    <h1>Ski Manager Ops</h1>
    <p class="lc-sub">Authorised access only</p>
    <form method="POST" autocomplete="off">
      <input type="password" name="password" placeholder="Admin password" autofocus>
      <button type="submit" class="lc-btn">Sign in &#x2192;</button>
      <?php if ($error): ?><div class="lc-err">&#x26A0; <?=htmlspecialchars($error)?></div><?php endif; ?>
    </form>
  </div>
</div>

<?php else: ?>

<div id="hdr">
  <div class="brand">
    <span class="brand-ico">&#x2699;&#xFE0F;</span>
    <div>
      <div class="brand-name">Ops</div>
    </div>
  </div>
  <div class="vsep"></div>
  <div class="tabs">
    <button class="tab active" data-panel="terminal">Terminal</button>
    <button class="tab" data-panel="overview">Overview</button>
    <button class="tab" data-panel="logs">Logs</button>
  </div>
  <div id="hdr-r">
    <div class="rdot" id="rdot"></div>
    <span id="clock"></span>
    <span class="upchip" id="upchip">&#x2026;</span>
    <a class="lout" href="?logout=1">Sign out</a>
  </div>
</div>

<div id="stats">
  <div class="stat">
    <div class="slbl">CPU</div>
    <div class="srow"><span class="sval" id="s-cpu">&#x2014;</span><span class="ssub" id="s-cpu-pct"></span></div>
    <div class="sbar"><div class="sfill" id="b-cpu" style="width:0%"></div></div>
  </div>
  <div class="stat">
    <div class="slbl">Memory</div>
    <div class="srow"><span class="sval" id="s-mem">&#x2014;</span><span class="ssub" id="s-mem-pct"></span></div>
    <div class="sbar"><div class="sfill" id="b-mem" style="width:0%"></div></div>
  </div>
  <div class="stat">
    <div class="slbl">Disk</div>
    <div class="srow"><span class="sval" id="s-disk">&#x2014;</span><span class="ssub" id="s-disk-pct"></span></div>
    <div class="sbar"><div class="sfill" id="b-disk" style="width:0%"></div></div>
  </div>
  <div class="stat">
    <div class="slbl">Load (1m)</div>
    <div class="srow"><span class="sval" id="s-load">&#x2014;</span></div>
  </div>
  <div class="stat">
    <div class="slbl">Uptime</div>
    <div class="srow"><span class="sval" id="s-up">&#x2014;</span></div>
  </div>
</div>

<div id="main">

  <div id="sb">
    <div class="ss">
      <div class="ss-hd">
        <span class="ss-title">Services</span>
        <span class="ss-badge" id="svc-badge">&#x2014;</span>
      </div>
      <div class="ss-body" id="svc-list"><div style="font-size:.71rem;color:var(--tx3);padding:4px 4px">Checking&#x2026;</div></div>
    </div>
    <div class="ss">
      <div class="ss-hd">
        <span class="ss-title">Containers</span>
        <span class="ss-badge" id="dk-badge">&#x2014;</span>
      </div>
      <div class="ss-body" id="dk-list"><div style="font-size:.71rem;color:var(--tx3);padding:4px 4px">Loading&#x2026;</div></div>
    </div>
    <div class="ss">
      <div class="ss-hd"><span class="ss-title">Quick Access</span></div>
      <div class="lgrp">
        <a class="lk" href="https://ski-manager.net" target="_blank" rel="noopener"><span class="lk-ico">&#x26F7;&#xFE0F;</span> Game</a>
        <a class="lk" href="https://ski-manager.net/admin" target="_blank" rel="noopener"><span class="lk-ico">&#x1F527;</span> Admin</a>
        <a class="lk" href="https://panel.ski-manager.cloud" target="_blank" rel="noopener"><span class="lk-ico">&#x1F5A5;&#xFE0F;</span> CloudPanel</a>
        <a class="lk" href="https://whodb.ski-manager.cloud" target="_blank" rel="noopener"><span class="lk-ico">&#x1F5C4;&#xFE0F;</span> WhoDB</a>
        <a class="lk" href="https://xyops.ski-manager.cloud" target="_blank" rel="noopener"><span class="lk-ico">&#x1F4CA;</span> XyOps</a>
        <a class="lk" href="https://terminal.ski-manager.cloud" target="_blank" rel="noopener"><span class="lk-ico">&#x1F4BB;</span> Terminal</a>
        <a class="lk" href="/ski-manager-v102-release.aab" download="ski-manager-v102-release.aab"><span class="lk-ico">&#x1F4E6;</span> AAB v1.2</a>
        <a class="lk" href="/ski-manager-v101-debug.apk" download="ski-manager-v101-debug.apk"><span class="lk-ico">&#x1F916;</span> APK debug</a>
        <a class="lk" href="https://play.google.com/console" target="_blank" rel="noopener"><span class="lk-ico">&#x1F3EA;</span> Play Console</a>
      </div>
    </div>
    <div class="ss">
      <div class="ss-hd"><span class="ss-title">VPS</span></div>
      <div class="ss-body">
        <div class="vps-grid">
          <span class="vg-lbl">IP</span><span class="vg-val">72.62.170.146</span>
          <span class="vg-lbl">SSH</span><span class="vg-val">:22</span>
          <span class="vg-lbl">DB</span><span class="vg-val">:3306</span>
          <span class="vg-lbl">Panel</span><span class="vg-val">:8443</span>
        </div>
      </div>
    </div>
    <div class="ss">
      <div class="ss-hd"><span class="ss-title">Deploy</span></div>
      <div class="dep-btns">
        <button class="dep-btn dep-vercel" onclick="openDeploy('vercel')">
          <span class="dep-ico">▲</span>
          <span class="dep-lbl">Vercel<div class="dep-env">ski-manager.cloud</div></span>
        </button>
        <button class="dep-btn dep-beta" onclick="openDeploy('nginx-beta')">
          <span class="dep-ico">&#x1F7E6;</span>
          <span class="dep-lbl">NGINX &rarr; Beta<div class="dep-env">beta.ski-manager.net</div></span>
        </button>
        <button class="dep-btn dep-prod" onclick="openDeploy('nginx-prod')">
          <span class="dep-ico">&#x1F534;</span>
          <span class="dep-lbl">NGINX &rarr; Production<div class="dep-env">ski-manager.net</div></span>
        </button>
      </div>
    </div>
  </div>

  <div id="content">

    <div id="p-terminal" class="panel active">
      <div class="thdr">
        <div class="tdots">
          <div class="td r" title="Overview" onclick="switchTab('overview')"></div>
          <div class="td y"></div>
          <div class="td g" title="Pop out" onclick="window.open('https://terminal.ski-manager.cloud','_blank')"></div>
        </div>
        <span class="ttl">root@72.62.170.146 &#x2014; VPS Terminal</span>
        <div class="thdr-r">
          <button class="thdr-btn" onclick="document.getElementById('term-frame').src='/terminal/'">&#x21BA; Reconnect</button>
          <a class="thdr-btn" href="https://terminal.ski-manager.cloud" target="_blank" rel="noopener">&#x2197; Pop out</a>
        </div>
      </div>
      <iframe id="term-frame" src="/terminal/" allow="clipboard-read; clipboard-write" title="VPS Terminal"></iframe>
    </div>

    <div id="p-overview" class="panel">
      <div class="ov-col">
        <div class="card">
          <div class="card-hdr">Services <span class="card-badge" id="ov-svc-badge">&#x2014;</span></div>
          <div id="ov-svc-body"><div class="log-empty">Loading&#x2026;</div></div>
        </div>
      </div>
      <div class="ov-col" style="flex:2;min-width:310px">
        <div class="card">
          <div class="card-hdr">Docker Containers <span class="card-badge" id="ov-dk-badge">&#x2014;</span></div>
          <table class="dk-table">
            <thead><tr><th>Name</th><th>Image</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="dk-tbody"><tr><td colspan="4" style="text-align:center;color:var(--tx3);padding:16px">Loading&#x2026;</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="p-logs" class="panel">
      <div class="log-bar">
        <label>Container</label>
        <select id="log-ctr"><option value="">&#x2014; select &#x2014;</option></select>
        <label style="margin-left:8px">Lines</label>
        <select id="log-lines">
          <option value="100">100</option>
          <option value="200">200</option>
          <option value="500">500</option>
          <option value="50">50</option>
        </select>
        <div class="log-acts">
          <button class="log-btn on" id="log-follow-btn" onclick="toggleFollow()">&#x27F3; Follow</button>
          <button class="log-btn" onclick="fetchLogs()">&#x21BA; Refresh</button>
          <button class="log-btn" onclick="clearLogs()">&#x2715; Clear</button>
        </div>
      </div>
      <div id="log-out"><div class="log-empty">Select a container to view its logs.</div></div>
    </div>

  </div>
</div>

<div id="toast-stack"></div>

<div class="overlay" id="upd-overlay" onclick="if(event.target===this)closeUpdateModal()">
  <div class="modal">
    <div class="mhdr">
      <div class="mhdr-ico">&#x1F4E6;</div>
      <div class="mhdr-text">
        <div class="mhdr-title" id="upd-title">Update Container</div>
        <div class="mhdr-sub" id="upd-sub"></div>
      </div>
      <button class="mbtn-close" onclick="closeUpdateModal()">&#x2715; Close</button>
    </div>
    <div class="mbody">
      <div class="mlog" id="upd-log"></div>
    </div>
    <div class="mfoot">
      <span class="mstatus" id="upd-status"></span>
      <button class="mbtn mbtn-sec" onclick="closeUpdateModal()">Cancel</button>
      <button class="mbtn mbtn-upd" id="upd-btn" onclick="runUpdate()">Pull &amp; Update</button>
    </div>
  </div>
</div>

<div class="overlay" id="dep-overlay" onclick="if(event.target===this)closeDeploy()">
  <div class="modal">
    <div class="mhdr">
      <div class="mhdr-ico" id="dep-ico">&#x1F680;</div>
      <div class="mhdr-text">
        <div class="mhdr-title" id="dep-title">Deploy</div>
        <div class="mhdr-sub" id="dep-sub"></div>
      </div>
      <button class="mbtn-close" id="dep-close-btn" onclick="closeDeploy()">&#x2715; Close</button>
    </div>
    <div class="mbody">
      <div id="dep-confirm-wrap" class="dep-confirm" style="display:none">
        <input type="checkbox" id="dep-confirm-chk" onchange="document.getElementById('dep-run-btn').disabled=!this.checked">
        <label for="dep-confirm-chk">I understand this will <strong>overwrite production</strong> (ski-manager.net) — deploy now.</label>
      </div>
      <div class="dlog" id="dep-log">Ready. Press &ldquo;Deploy&rdquo; to start.</div>
    </div>
    <div class="mfoot">
      <span class="mstatus" id="dep-status"></span>
      <button class="mbtn mbtn-sec" onclick="closeDeploy()">Cancel</button>
      <button class="mbtn mbtn-upd" id="dep-run-btn" onclick="runDeploy()">&#x1F680; Deploy</button>
    </div>
  </div>
</div>

<script>
(function(){'use strict';

(function tick(){
  var n=new Date();
  document.getElementById('clock').textContent=
    String(n.getHours()).padStart(2,'0')+':'+
    String(n.getMinutes()).padStart(2,'0')+':'+
    String(n.getSeconds()).padStart(2,'0');
  setTimeout(tick,1000);
})();

document.addEventListener('keydown',function(e){
  if(e.key==='Escape'){closeUpdateModal();return;}
  if(e.target.tagName==='INPUT'||e.target.tagName==='TEXTAREA'||e.target.tagName==='SELECT')return;
  if(e.key==='t'||e.key==='T')switchTab('terminal');
  if(e.key==='o'||e.key==='O')switchTab('overview');
  if(e.key==='l'||e.key==='L')switchTab('logs');
});

window.switchTab=function(name){
  document.querySelectorAll('.tab').forEach(function(t){t.classList.toggle('active',t.dataset.panel===name);});
  document.querySelectorAll('.panel').forEach(function(p){p.classList.toggle('active',p.id==='p-'+name);});
};
document.querySelectorAll('.tab').forEach(function(t){t.addEventListener('click',function(){switchTab(t.dataset.panel);});});

function setBar(id,pct){
  var el=document.getElementById(id);if(!el)return;
  el.style.width=pct+'%';
  el.className='sfill'+(pct>=85?' c':pct>=65?' w':'');
}

function toast(msg,type){
  var s=document.getElementById('toast-stack');
  var d=document.createElement('div');
  d.className='toast '+(type||'ok');
  d.textContent=msg;
  s.appendChild(d);
  requestAnimationFrame(function(){d.classList.add('in');});
  setTimeout(function(){d.classList.remove('in');setTimeout(function(){if(d.parentNode)d.parentNode.removeChild(d);},280);},3000);
}

function flashRefresh(){
  var d=document.getElementById('rdot');
  d.classList.remove('pulse');void d.offsetWidth;d.classList.add('pulse');
}

async function api(endpoint,body){
  var opts={headers:{'Content-Type':'application/x-www-form-urlencoded'}};
  if(body){opts.method='POST';opts.body=new URLSearchParams(body);}
  var r=await fetch('?api='+endpoint,opts);return r.json();
}

async function refreshStats(){
  try{
    var d=await api('stats');
    document.getElementById('s-cpu').textContent=d.cpu+'%';setBar('b-cpu',d.cpu);
    document.getElementById('s-mem').textContent=d.mem.used+' / '+d.mem.total+' GB';
    document.getElementById('s-mem-pct').textContent=d.mem.pct+'%';setBar('b-mem',d.mem.pct);
    document.getElementById('s-disk').textContent=d.disk.used+' / '+d.disk.total+' GB';
    document.getElementById('s-disk-pct').textContent=d.disk.pct+'%';setBar('b-disk',d.disk.pct);
    document.getElementById('s-load').textContent=d.load;
    document.getElementById('s-up').textContent=d.uptime;
    document.getElementById('upchip').textContent='up '+d.uptime;
  }catch(e){}
}

async function refreshServices(){
  try{
    var svcs=await api('services');
    var up=svcs.filter(function(s){return s.up;}).length;
    document.getElementById('svc-badge').textContent=up+'/'+svcs.length;
    document.getElementById('ov-svc-badge').textContent=up+'/'+svcs.length+' online';
    var sb='';
    svcs.forEach(function(s){
      var ext=s.ext?'<a class="svx" href="'+s.ext+'" target="_blank" rel="noopener">&#x2197;</a>':'';
      sb+='<div class="sv"><div class="dot '+(s.up?'up':'dn')+'"></div><span class="svn">'+s.icon+' '+s.name+'</span><span class="svt'+(s.up?'':' dn')+'">'+(s.up?s.ms+'ms':'down')+'</span>'+ext+'</div>';
    });
    document.getElementById('svc-list').innerHTML=sb;
    var ob='';
    svcs.forEach(function(s){
      var badge=s.up?'<span class="ov-badge up">'+s.ms+'ms</span>':'<span class="ov-badge dn">down</span>';
      var href=s.ext?'href="'+s.ext+'" target="_blank" rel="noopener"':'href="#" onclick="return false"';
      ob+='<a class="ov-svc" '+href+'><div class="ov-ico">'+s.icon+'</div><div class="ov-info"><div class="ov-nm">'+s.name+'</div><div class="ov-sub">'+(s.ext||'internal')+'</div></div>'+badge+'</a>';
    });
    document.getElementById('ov-svc-body').innerHTML=ob;
  }catch(e){}
}

async function refreshDocker(){
  try{
    var cs=await api('docker');
    var run=cs.filter(function(c){return c.up;}).length;
    document.getElementById('dk-badge').textContent=run+'/'+cs.length;
    document.getElementById('ov-dk-badge').textContent=run+'/'+cs.length+' running';
    var sel=document.getElementById('log-ctr');var cur=sel.value;
    sel.innerHTML='<option value="">&#x2014; select &#x2014;</option>';
    cs.forEach(function(c){var o=document.createElement('option');o.value=c.name;o.textContent=c.name+(c.up?'':' (stopped)');if(c.name===cur)o.selected=true;sel.appendChild(o);});
    var sb='';
    cs.forEach(function(c){
      var acts='<div class="dka">';
      if(c.up){acts+='<button class="ab r" onclick="dockerAct(\''+c.name+'\',\'restart\')">&#x21BA;</button><button class="ab s" onclick="dockerAct(\''+c.name+'\',\'stop\')">&#x25A0;</button>';}
      else{acts+='<button class="ab g" onclick="dockerAct(\''+c.name+'\',\'start\')">&#x25B6;</button>';}
      acts+='<button class="ab u" onclick="openUpdateModal(\''+c.name+'\',\''+c.full_image+'\')">&#x2191;</button></div>';
      sb+='<div class="dk"><div class="dot '+(c.up?'up':'dn')+'"></div><span class="dkn" title="'+c.name+'">'+c.name+'</span><span class="dks">'+c.status+'</span>'+acts+'</div>';
    });
    document.getElementById('dk-list').innerHTML=sb;
    var rows='';
    cs.forEach(function(c){
      var dot='<span class="st-dot"><span class="dot '+(c.up?'up':'dn')+'"></span>'+c.status+'</span>';
      var acts='';
      if(c.up){acts+='<button class="dk-act r" onclick="dockerAct(\''+c.name+'\',\'restart\')">&#x21BA; Restart</button><button class="dk-act s" onclick="dockerAct(\''+c.name+'\',\'stop\')">&#x25A0; Stop</button>';}
      else{acts+='<button class="dk-act g" onclick="dockerAct(\''+c.name+'\',\'start\')">&#x25B6; Start</button>';}
      acts+='<button class="dk-act u" onclick="openUpdateModal(\''+c.name+'\',\''+c.full_image+'\')">&#x2191; Update</button>';
      rows+='<tr><td style="font-weight:600;color:#e4edf7">'+c.name+'</td><td style="color:var(--tx3);font-family:var(--mono);font-size:.67rem">'+c.image+'</td><td>'+dot+'</td><td>'+acts+'</td></tr>';
    });
    document.getElementById('dk-tbody').innerHTML=rows||'<tr><td colspan="4" style="text-align:center;color:var(--tx3);padding:16px">No containers</td></tr>';
  }catch(e){}
}

window.dockerAct=async function(name,action){
  var lbl={restart:'Restarting',stop:'Stopping',start:'Starting'};
  toast((lbl[action]||action)+' '+name+'&#x2026;');
  try{
    var r=await api('docker_action',{name:name,action:action});
    toast(r.ok?'&#x2713; '+name+' '+action+'ed':'&#x2717; '+(r.out||'failed'),r.ok?'ok':'er');
    if(r.ok)setTimeout(refreshDocker,2200);
  }catch(e){toast('Request failed','er');}
};

var _logFollow=true,_logTimer=null;
window.toggleFollow=function(){
  _logFollow=!_logFollow;
  document.getElementById('log-follow-btn').classList.toggle('on',_logFollow);
  if(_logFollow&&document.getElementById('log-ctr').value)startLogFollow();else stopLogFollow();
};
function stopLogFollow(){clearInterval(_logTimer);_logTimer=null;}
function startLogFollow(){stopLogFollow();if(_logFollow&&document.getElementById('log-ctr').value)_logTimer=setInterval(fetchLogs,4000);}
window.fetchLogs=async function(){
  var name=document.getElementById('log-ctr').value;if(!name)return;
  var lines=document.getElementById('log-lines').value||100;
  try{
    var r=await api('logs',{name:name,lines:lines});
    var out=document.getElementById('log-out');
    if(r.ok&&r.log){out.textContent=r.log;out.scrollTop=out.scrollHeight;}
    else{out.innerHTML='<div class="log-empty">No output, or container not found.</div>';}
  }catch(e){document.getElementById('log-out').innerHTML='<div class="log-empty">Failed to fetch logs.</div>';}
};
window.clearLogs=function(){document.getElementById('log-out').innerHTML='<div class="log-empty">Cleared.</div>';};
document.getElementById('log-ctr').addEventListener('change',function(){if(this.value){fetchLogs();startLogFollow();}else stopLogFollow();});
document.getElementById('log-lines').addEventListener('change',fetchLogs);

var _updName='',_updRunning=false;
window.openUpdateModal=function(name,image){
  _updName=name;_updRunning=false;
  document.getElementById('upd-title').textContent='Update \u2014 '+name;
  document.getElementById('upd-sub').textContent=image;
  document.getElementById('upd-log').innerHTML='<span class="dim">Ready to update <strong style="color:#e4edf7">'+name+'</strong>.\n\nSteps:\n  1. Pull latest image from registry\n  2. Compare digest \u2014 skip restart if unchanged\n  3. Stop &amp; remove current container\n  4. Recreate with identical config\n  5. Start new container\n\nPress <strong style="color:#e4edf7">Pull &amp; Update</strong> to begin.</span>';
  document.getElementById('upd-status').innerHTML='';
  var btn=document.getElementById('upd-btn');btn.disabled=false;btn.textContent='Pull & Update';
  document.getElementById('upd-overlay').classList.add('open');
};
window.closeUpdateModal=function(){if(_updRunning)return;document.getElementById('upd-overlay').classList.remove('open');};
window.runUpdate=async function(){
  if(_updRunning||!_updName)return;
  _updRunning=true;
  var btn=document.getElementById('upd-btn'),logEl=document.getElementById('upd-log'),statusEl=document.getElementById('upd-status');
  btn.disabled=true;btn.innerHTML='<span class="spin"></span>Updating\u2026';
  statusEl.innerHTML='<span class="spin"></span> Pulling image\u2026';logEl.textContent='';
  try{
    var r=await api('update',{name:_updName});
    var html=(r.log||'(no output)').replace(/\u2713[^\n]*/g,function(m){return'<span class="ok">'+m+'</span>';}).replace(/\u2717[^\n]*/g,function(m){return'<span class="er">'+m+'</span>';}).replace(/(\u00bb[^\n]*)/g,'<span style="color:#e4edf7">$1</span>').replace(/( {2}[^\n]*)/g,'<span class="dim">$1</span>');
    logEl.innerHTML=html;logEl.scrollTop=logEl.scrollHeight;
    if(r.ok&&r.updated){statusEl.innerHTML='<span class="bk-ok">\u2713 Updated</span>';toast('\u2713 '+_updName+' updated','ok');}
    else if(r.ok&&r.updated===false){statusEl.innerHTML='<span class="bk-same">Already up to date</span>';toast(_updName+' already up to date');}
    else{statusEl.innerHTML='<span class="bk-er">\u2717 Failed</span>';toast('\u2717 Update failed','er');}
    btn.textContent='Pull & Update';if(r.ok)setTimeout(refreshDocker,1500);
  }catch(e){logEl.textContent='Request error: '+e.message;statusEl.innerHTML='<span class="bk-er">\u2717 Error</span>';btn.textContent='Pull & Update';toast('Request failed','er');}
  _updRunning=false;btn.disabled=false;
};

function refreshAll(){flashRefresh();refreshStats();refreshServices();refreshDocker();}
refreshAll();setInterval(refreshAll,30000);

// ── DEPLOY ────────────────────────────────────────────────────────────────────
var _depTarget='', _depRunning=false, _depEs=null;

var DEP_META = {
  'vercel':     { label:'Deploy to Vercel',       sub:'ski-manager.cloud → npx vercel --prod', ico:'▲',  prod:false },
  'nginx-beta': { label:'NGINX → Beta',           sub:'/root/deploy-beta.sh → beta.ski-manager.net', ico:'🟦', prod:false },
  'nginx-prod': { label:'NGINX → Production',     sub:'/root/deploy-prod.sh → ski-manager.net',      ico:'🔴', prod:true  },
};

window.openDeploy = function(target) {
  var m = DEP_META[target];
  if (!m) return;
  _depTarget  = target;
  _depRunning = false;

  document.getElementById('dep-ico').textContent    = m.ico;
  document.getElementById('dep-title').textContent  = m.label;
  document.getElementById('dep-sub').textContent    = m.sub;
  document.getElementById('dep-log').innerHTML      = 'Ready. Press \u201cDeploy\u201d to start.';
  document.getElementById('dep-status').textContent = '';
  document.getElementById('dep-run-btn').disabled   = m.prod; // prod disabled until checkbox
  document.getElementById('dep-run-btn').textContent= '\uD83D\uDE80 Deploy';

  var cw = document.getElementById('dep-confirm-wrap');
  var ck = document.getElementById('dep-confirm-chk');
  ck.checked = false;
  cw.style.display = m.prod ? 'flex' : 'none';

  document.getElementById('dep-overlay').classList.add('open');
};

window.closeDeploy = function() {
  if (_depRunning) return;
  if (_depEs) { _depEs.close(); _depEs = null; }
  document.getElementById('dep-overlay').classList.remove('open');
};

window.runDeploy = function() {
  if (_depRunning) return;
  var isProd = DEP_META[_depTarget] && DEP_META[_depTarget].prod;
  var confirm = isProd ? '1' : '0';

  _depRunning = true;
  var btn  = document.getElementById('dep-run-btn');
  var stat = document.getElementById('dep-status');
  var log  = document.getElementById('dep-log');
  var close= document.getElementById('dep-close-btn');

  btn.disabled  = true;
  close.disabled= true;
  btn.innerHTML = '<span class="spin"></span>Deploying\u2026';
  log.textContent = '';
  stat.textContent = '';

  // Disable all deploy buttons in sidebar during run
  document.querySelectorAll('.dep-btn').forEach(function(b){b.disabled=true;});

  var url = '?deploy=' + encodeURIComponent(_depTarget) + '&confirm=' + confirm;
  _depEs = new EventSource(url);

  _depEs.onmessage = function(e) {
    var d;
    try { d = JSON.parse(e.data); } catch(_) { return; }

    if (d.err) {
      appendDepLog(log, d.err, 'dl-fail');
      finishDeploy(false, btn, stat, close);
      return;
    }
    if (d.line !== undefined) {
      appendDepLog(log, d.line, d.type === 'stderr' ? 'dl-err' : (d.type === 'info' ? 'dl-info' : 'dl-out'));
    }
    if (d.done) {
      var ok = d.exit === 0;
      appendDepLog(log, ok ? '\u2714 Deploy finished successfully.' : '\u2718 Deploy exited with code ' + d.exit, ok ? 'dl-ok' : 'dl-fail');
      finishDeploy(ok, btn, stat, close);
    }
  };
  _depEs.onerror = function() {
    if (_depRunning) {
      appendDepLog(log, '\u2718 Stream error or connection lost.', 'dl-fail');
      finishDeploy(false, btn, stat, close);
    }
  };
};

function appendDepLog(log, text, cls) {
  var span = document.createElement('span');
  span.className = cls || '';
  span.textContent = text + '\n';
  log.appendChild(span);
  log.scrollTop = log.scrollHeight;
}

function finishDeploy(ok, btn, stat, close) {
  _depRunning = false;
  if (_depEs) { _depEs.close(); _depEs = null; }
  btn.disabled   = false;
  close.disabled = false;
  btn.innerHTML  = '\uD83D\uDE80 Deploy again';
  stat.textContent = ok ? '\u2714 Success' : '\u2718 Failed';
  stat.style.color = ok ? 'var(--green-t)' : 'var(--red)';
  document.querySelectorAll('.dep-btn').forEach(function(b){b.disabled=false;});
  toast(ok ? '\u2714 Deploy succeeded' : '\u2718 Deploy failed', ok ? 'ok' : 'er');
}

})();
</script>

<?php endif; ?>
</body>
</html>
