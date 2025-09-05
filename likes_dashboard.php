<?php
// likes_dashboard.php - Responsive dashboard for post like counts

session_start();

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$full_domain = $protocol . '://' . $domain . '/';

if (isset($_SESSION["$full_domain"]['user']) && !empty($_SESSION["$full_domain"]['user'])) {
    $user = $_SESSION["$full_domain"]['user'];
    $db = new SQLite3(__DIR__ . '/likes.db');

    // Pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 20;
    $offset = ($page - 1) * $perPage;

    // Filtering
    $filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';
    $where = $filter ? "WHERE url LIKE '%" . $db->escapeString($filter) . "%'" : '';

    // Get total count
    $total = $db->querySingle("SELECT COUNT(*) FROM likes $where");
    $pages = ceil($total / $perPage);

    // Get paginated results
    $res = $db->query("SELECT url, count FROM likes $where ORDER BY count DESC LIMIT $perPage OFFSET $offset");
    $rows = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $rows[] = $row;
    }
    ?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Post Like Counts Dashboard</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f8f9fa; }
            .container { max-width: 900px; margin: 30px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #ccc; padding: 24px; }
            h1 { margin-top: 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 18px; }
            th, td { padding: 10px 8px; border-bottom: 1px solid #eee; }
            th { background: #3798D8; color: #fff; }
            tr:hover { background: #f1f7ff; }
            .pagination { margin: 18px 0; text-align: center; }
            .pagination a { margin: 0 4px; padding: 6px 12px; background: #3798D8; color: #fff; border-radius: 4px; text-decoration: none; }
            .pagination a.active { background: #217dbb; font-weight: bold; }
            .filter-form { margin-bottom: 12px; }
            @media (max-width: 600px) {
                .container { padding: 8px; }
                th, td { padding: 6px 4px; }
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Post Like Counts</h1>
        <form class="filter-form" method="get" action="">
            <input type="text" name="filter" value="<?php echo htmlspecialchars($filter); ?>" placeholder="Filter by URL..." style="padding:6px 10px;width:220px;">
            <button type="submit" style="padding:6px 14px;background:#3798D8;color:#fff;border:none;border-radius:4px;">Filter</button>
        </form>
        <table>
            <thead>
                <tr><th>Post URL</th><th>Like Count</th></tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td style="word-break:break-all;"><a href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank"><?php echo htmlspecialchars($row['url']); ?></a></td>
                    <td><?php echo $row['count']; ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
                <tr><td colspan="2" style="text-align:center;color:#888;">No results found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?<?php echo http_build_query(['filter'=>$filter,'page'=>$i]); ?>"<?php if ($i == $page) echo ' class="active"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
    </body>
    </html>

<?php
} else {
    header('Location: ' . $full_domain);
    exit;
}
?>