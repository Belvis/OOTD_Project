<?php
$title = "OOTD - Mes Préférences"; 
$description = "Tout ce que vous aimez"; 
include 'include/header.inc.php'; 
include 'util/database.php'; 
include 'include/function.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit(); 
}

$user_id = $_SESSION['user_id'];
$images = getImagesToLike($user_id);
?>
<div id="form-container">
    <div id="carousel-container">
        <div class="carousel" id="carousel">
        <?php foreach ($images as $image): ?>
            <div id="carousel-item" id="carousel"  data-id="<?= $image['image_id']; ?>">
                <img src="<?= $image['image_url']; ?>" alt="Image">
                <div class="thumbs">
                    <!-- Icône pouce haut (liker) -->
                    <i class="thumb-up" onclick="handleAction(<?= $image['image_id']; ?>)">👍</i>
                    
                    <!-- Icône pouce bas (ne pas liker) -->
                    <i class="thumb-down" onclick="handleAction(<?= $image['image_id']; ?>)">👎</i>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <div class="arrow left-arrows" id="left-arrow " onclick="scrollCarousel('left')">
        <i class="fas fa-chevron-left"></i>
    </div>
    <div class="arrow right-arrows" id="right-arrow" onclick="scrollCarousel('right')">
        <i class="fas fa-chevron-right"></i>
    </div>
    </div>
        
</div>
</main>
<?php include 'include/footer.inc.php'; ?>
