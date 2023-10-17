<section class="user-content">
    <button class="back-button" data-translation="generic->back"><i class="fas fa-arrow-left"></i></button>
    <?php require dirname(__DIR__, 3) . "/miscellaneous/spinner/index.php"; ?>
    <div class="empty-folder">
        <h2></h2>
        <img src="assets/img/miscellaneous/empty.svg" alt="" aria-hidden="true">
    </div>
    <section class="folders-container"></section>
    <button class="load-more" id="folders-load-more" data-translation="generic->load_more"><i class="fas fa-sync-alt"></i></button>
    <section class="files-container"></section>
    <button class="load-more" id="files-load-more" data-translation="generic->load_more"><i class="fas fa-sync-alt"></i></button>
    <?php require __DIR__ . "/context_menu/index.html"; ?>
</section>