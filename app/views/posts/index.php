<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container postContainer">
  <div class="container sticky-top pt-1 bg-light">
    <?php flash('post_message'); ?>
    <div class="row mb-3">
      <div class="col-6">
        <h1>Posts</h1>
      </div>
      <div class="col-6">
        <a href="<?php echo URLROOT; ?>/posts/add" class="btn btn-primary float-end">
          <i class="fa fa-pencil"></i> Add Post
        </a>
      </div>
    </div>
  </div>
  <?php foreach ($data['posts'] as $post) : ?>
  <div  class="card card-body mb-3" >
    <h4 class="card-title"><?= $post->title ?> 
    <?php if($_SESSION['user_id'] == $post->user_id): ?>
      <a href="<?php echo URLROOT; ?>/posts/edit/<?= $post->post_id ?>" class="btn ope edit"><i class="fa-solid fa-pen-to-square"></i></a>
      <a href="<?php echo URLROOT; ?>/posts/delete/<?= $post->post_id ?>" class="btn ope delete"><i class="fa-solid fa-trash-can"></i></a>
    <?php endif; ?>
  
  </h4>
    
    <div class="bg-light p-2 mb-3 rounded">
      <span class=" text-muted">Written by</span> <?= $post->first_name ?> <?= $post->last_name ?> <span class="text-muted">on</span> <?= $post->published_at ?>
    </div>
    <p class="card-text"><?= $post->post_body ?></p>
    <img class="postImage" src="<?= $post->image ?>" alt="post Image">
    <div class="container impression">
      <div class=" d-flex justify-content-between">
        <div data-postId="<?= $post->post_id ?>" onclick="likePost(<?= $post->post_id ?>)" class="p-1 like d-flex gap-1"><i class="fa-solid fa-thumbs-up"></i>Like <span id="like<?= $post->post_id ?>" ><?= $post->likes ?></span></div>
        <div class="p-1 comment d-flex gap-1"><i class="fa-solid fa-comment"></i>Comment <span id="cn<?= $post->post_id ?>"><?= $post->comments ?></span></div>
      </div>
        <form class="row submitcomment">
          <input type="text" class="pos" name="pos" value="<?= $post->post_id ?>" readonly style="display: none;">
          <div class="col-9 p-1"><input class="form-control w-100 p-1" value="<?= ($post->post_id == $data['pos'])? $data['recentComment'] : '' ; ?>" type="text" name="comm"></div>
          <div class="col-3 p-1"><input class="btn form_elem text-light  w-100 p-1" type="submit"  value="submit"></div>
          <span class=" invalid-feedback" style="display: block;margin-top: -7px;"><?= ($post->post_id == $data['pos'])?  $data['recentComment_err']:''; ?></span>
        </form>

        <div class="comments" id="comments<?= $post->post_id ?>">
        <?php foreach ($data['comments'][$post->post_id] as $comm) : ?>
          <div class="comment">
            <p><?= $comm->comment_body  ?></p>
            <div class="d-flex justify-content-between">
              <span class="date d-block"><?= $comm->first_name ?> <?= $comm->last_name ?></span>
              <span class="date d-block ms-auto"><?= $comm->published_at ?></span>
            </div>
          </div>
        <?php endforeach; ?>
        </div>
    </div>
  </div>
  <?php endforeach; ?>

</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>