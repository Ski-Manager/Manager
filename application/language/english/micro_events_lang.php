<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// -----------------------------------------------------------------------
// Micro-events UI strings
// -----------------------------------------------------------------------
$lang['micro_events']['modal_title']        = '⚡ Quick Decision';
$lang['micro_events']['expires_in']         = 'This event expires in 24 hours.';
$lang['micro_events']['loading']            = 'Processing your decision…';

// -----------------------------------------------------------------------
// vip_queue_jump
// A VIP guest insists on skipping the ski lift queue.
// Choice A: Let them through (+€800, -3 rep)
// Choice B: Enforce the rules (0, +4 rep)
// -----------------------------------------------------------------------
$lang['micro_events']['vip_queue_jump_title']       = 'VIP Queue Jump Request';
$lang['micro_events']['vip_queue_jump_description'] = 'A well-known VIP guest is demanding to skip the ski lift queue. Other guests are watching carefully.';
$lang['micro_events']['vip_queue_jump_a_label']     = 'Let them through';
$lang['micro_events']['vip_queue_jump_a_hint']      = '+€800, -3 reputation';
$lang['micro_events']['vip_queue_jump_b_label']     = 'Enforce the rules';
$lang['micro_events']['vip_queue_jump_b_hint']      = '+4 reputation';
$lang['micro_events']['vip_queue_jump_a_result']    = 'The VIP is delighted and tips your staff generously. However some guests grumbled at the preferential treatment.';
$lang['micro_events']['vip_queue_jump_b_result']    = 'You held firm. Other guests applauded the fair treatment and your resort\'s reputation got a small boost.';

// -----------------------------------------------------------------------
// press_interview
// A journalist requests a quick resort interview.
// Choice A: Give the interview (-€500, +8 rep)
// Choice B: Decline politely (0, -2 rep)
// -----------------------------------------------------------------------
$lang['micro_events']['press_interview_title']       = 'Press Interview Request';
$lang['micro_events']['press_interview_description'] = 'A journalist from a popular travel magazine wants a quick interview about your resort. This could be great publicity.';
$lang['micro_events']['press_interview_a_label']     = 'Give the interview';
$lang['micro_events']['press_interview_a_hint']      = '+8 reputation, -€500';
$lang['micro_events']['press_interview_b_label']     = 'Decline politely';
$lang['micro_events']['press_interview_b_hint']      = '-2 reputation';
$lang['micro_events']['press_interview_a_result']    = 'The article was glowing! Your resort received excellent coverage and your reputation soared.';
$lang['micro_events']['press_interview_b_result']    = 'The journalist left disappointed and wrote a brief note that your resort was "unavailable for comment".';

// -----------------------------------------------------------------------
// equipment_deal
// A supplier offers a last-minute discount on rental equipment.
// Choice A: Accept the deal (-€400, +2 rep)
// Choice B: Pass this time (0, 0)
// -----------------------------------------------------------------------
$lang['micro_events']['equipment_deal_title']       = 'Last-Minute Equipment Deal';
$lang['micro_events']['equipment_deal_description'] = 'A ski equipment supplier is offering a significant discount on rental equipment for today only. Guests will notice the quality upgrade.';
$lang['micro_events']['equipment_deal_a_label']     = 'Accept the deal';
$lang['micro_events']['equipment_deal_a_hint']      = '-€400, +2 reputation';
$lang['micro_events']['equipment_deal_b_label']     = 'Pass this time';
$lang['micro_events']['equipment_deal_b_hint']      = 'No effect';
$lang['micro_events']['equipment_deal_a_result']    = 'Guests loved the brand-new equipment! Your rental shop earned a few extra compliments today.';
$lang['micro_events']['equipment_deal_b_result']    = 'You passed on the deal. Things continue as normal.';

// -----------------------------------------------------------------------
// lost_skier
// A skier has not returned from the mountain.
// Choice A: Deploy patrol immediately (-€300, +8 rep)
// Choice B: Wait and monitor (0, -12 rep)
// -----------------------------------------------------------------------
$lang['micro_events']['lost_skier_title']       = 'Missing Skier Report';
$lang['micro_events']['lost_skier_description'] = 'A skier has not returned to the base lodge. Their group is worried. What do you do?';
$lang['micro_events']['lost_skier_a_label']     = 'Deploy patrol immediately';
$lang['micro_events']['lost_skier_a_hint']      = '-€300, +8 reputation';
$lang['micro_events']['lost_skier_b_label']     = 'Wait and monitor';
$lang['micro_events']['lost_skier_b_hint']      = '-12 reputation';
$lang['micro_events']['lost_skier_a_result']    = 'Your patrol found the skier quickly and safely. Guests are full of praise for your resort\'s swift emergency response.';
$lang['micro_events']['lost_skier_b_result']    = 'After a long delay the skier was found unharmed, but the wait alarmed many guests. Your reputation took a hit.';
