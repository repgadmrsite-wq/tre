<?php
$breadcrumbs = $breadcrumbs ?? [];
?>
<?php if (!empty($breadcrumbs)): ?>
<section id="breadcrumb">
    <div class="container">
        <ul>
            <?php foreach ($breadcrumbs as $path => $title): ?>
                <li><a href="<?php echo $baseUrl . ltrim($path, '/'); ?>"><?php echo $title; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endif; ?>
