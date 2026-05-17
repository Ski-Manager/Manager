<?php
$CI =& get_instance();
$site_lang   = $CI->session->userdata('site_lang') ?: 'english';
$title_col   = ($site_lang === 'french') ? 'title_french'   : 'title_english';
$content_col = ($site_lang === 'french') ? 'content_french' : 'content_english';
$no_posts_msg = ($site_lang === 'french') ? 'Aucun article de blog trouvé.' : 'No blog posts found.';
$blog_label   = ($site_lang === 'french') ? 'Blog &amp; Actualités' : 'Blog &amp; News';
$read_more    = ($site_lang === 'french') ? 'Lire la suite' : 'Read more';
$posted_on    = ($site_lang === 'french') ? 'Publié le' : 'Posted on';
$page_label   = ($site_lang === 'french') ? 'Page' : 'Page';
$prev_label   = ($site_lang === 'french') ? '&laquo; Précédent' : '&laquo; Previous';
$next_label   = ($site_lang === 'french') ? 'Suivant &raquo;' : 'Next &raquo;';

// French month names for localised date display
$fr_months = [
    1=>'janvier',2=>'février',3=>'mars',4=>'avril',5=>'mai',6=>'juin',
    7=>'juillet',8=>'août',9=>'septembre',10=>'octobre',11=>'novembre',12=>'décembre',
];

function format_blog_date(string $date_str, string $lang, array $fr_months): string {
    $ts = strtotime($date_str);
    if (!$ts) return '';
    if ($lang === 'french') {
        return intval(date('j', $ts)) . ' ' . $fr_months[intval(date('n', $ts))] . ' ' . date('Y', $ts);
    }
    return date('F j, Y', $ts);
}
?>
<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body p-4 mb-3">

        <h2 class="h2 mb-1"><i class="fa-solid fa-newspaper mr-2"></i><?php echo $blog_label; ?></h2>
        <p class="text-base-content/60 mb-3"><?php echo ($site_lang === 'french')
            ? 'Les dernières nouvelles et mises à jour de Ski-Manager.'
            : 'The latest news and updates from Ski-Manager.'; ?></p>

        <!-- Search bar -->
        <?php
            $search_placeholder = ($site_lang === 'french') ? 'Rechercher des articles…' : 'Search posts…';
            $search_btn_label   = ($site_lang === 'french') ? 'Rechercher' : 'Search';
            $clear_label        = ($site_lang === 'french') ? 'Effacer la recherche' : 'Clear search';
            $no_results_msg     = ($site_lang === 'french') ? 'Aucun article ne correspond à votre recherche.' : 'No posts matched your search.';
            $results_for_label  = ($site_lang === 'french') ? 'Résultats pour' : 'Results for';
        ?>
        <form method="get" action="<?php echo base_url('blogs_controller'); ?>" class="blog-search-bar mb-3" role="search" aria-label="<?php echo $search_btn_label; ?>">
            <input type="search" name="q" class="blog-search-input"
                   placeholder="<?php echo htmlspecialchars($search_placeholder, ENT_QUOTES, 'UTF-8'); ?>"
                   value="<?php echo htmlspecialchars($search_query ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   aria-label="<?php echo htmlspecialchars($search_placeholder, ENT_QUOTES, 'UTF-8'); ?>"
                   maxlength="100">
            <button type="submit" class="btn btn-info btn-sm">
                <i class="fa-solid fa-magnifying-glass mr-1"></i><?php echo htmlspecialchars($search_btn_label, ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </form>
        <?php if (!empty($search_query)): ?>
        <p class="blog-search-clear">
            <i class="fa-solid fa-magnifying-glass mr-1"></i>
            <strong><?php echo htmlspecialchars($results_for_label, ENT_QUOTES, 'UTF-8'); ?>:</strong>
            &ldquo;<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>&rdquo;
            &nbsp;&mdash;&nbsp;
            <a href="<?php echo base_url('blogs_controller'); ?>"><?php echo htmlspecialchars($clear_label, ENT_QUOTES, 'UTF-8'); ?></a>
        </p>
        <?php endif; ?>

        <?php if (!empty($posts)): ?>
            <div class="grid grid-cols-12 gap-3 mb-4">
                <?php foreach ($posts as $post): ?>
                    <?php
                        $title   = htmlspecialchars($post->$title_col   ?? $post->title_english,   ENT_QUOTES, 'UTF-8');
                        $content = htmlspecialchars($post->$content_col ?? $post->content_english, ENT_QUOTES, 'UTF-8');
                        $date    = !empty($post->created_date)
                            ? format_blog_date((string)$post->created_date, $site_lang, $fr_months)
                            : '';
                    ?>
                    <div class="col">
                        <div class="card h-full shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $title; ?></h5>
                                <?php if ($date): ?>
                                <p class="text-base-content/60 small mb-2">
                                    <i class="fa-regular fa-calendar mr-1"></i><?php echo $posted_on; ?> <?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <?php endif; ?>
                                <p class="card-text"><?php echo nl2br($content); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isset($total_pages) && $total_pages > 1): ?>
            <?php $page_base_url = base_url('blogs_controller') . '?' . (!empty($search_query) ? 'q=' . urlencode($search_query) . '&' : '') . 'page='; ?>
            <nav aria-label="Blog pagination" class="flex justify-center mt-4">
                <div class="join">
                    <?php if ($current_page > 1): ?>
                    <a class="join-item btn btn-sm"
                       href="<?php echo $page_base_url . ($current_page - 1); ?>"
                       aria-label="<?php echo ($site_lang === 'french') ? 'Page précédente' : 'Previous page'; ?>"><?php echo $prev_label; ?></a>
                    <?php endif; ?>
                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <a class="join-item btn btn-sm <?php echo ($p === $current_page) ? 'btn-active' : ''; ?>"
                       href="<?php echo $page_base_url . $p; ?>"
                       <?php echo ($p === $current_page) ? 'aria-current="page"' : ''; ?>
                       aria-label="<?php echo htmlspecialchars($page_label . ' ' . $p . ($p === $current_page ? (($site_lang === 'french') ? ' (page actuelle)' : ' (current page)') : ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo $p; ?>
                    </a>
                    <?php endfor; ?>
                    <?php if ($current_page < $total_pages): ?>
                    <a class="join-item btn btn-sm"
                       href="<?php echo $page_base_url . ($current_page + 1); ?>"
                       aria-label="<?php echo ($site_lang === 'french') ? 'Page suivante' : 'Next page'; ?>"><?php echo $next_label; ?></a>
                    <?php endif; ?>
                </div>
            </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info">
                <i class="fa-solid fa-circle-info mr-2"></i>
                <?php echo !empty($search_query) ? htmlspecialchars($no_results_msg, ENT_QUOTES, 'UTF-8') : htmlspecialchars($no_posts_msg, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

    </div>
</div>
