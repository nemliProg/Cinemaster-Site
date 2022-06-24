<?php
class Posts extends Controller
{
  public function __construct()
  {

    if (!isLoggedIn()) {
      redirect('users/login');
    }

    $this->postModel = $this->model('Post');
    $this->userModel = $this->model('User');
    $this->commentModel = $this->model('Comment');
  }

  public function index()
  {

      $posts = $this->postModel->getPosts();
      $array = [];

      foreach ($posts as $post) {
        $array += [$post->post_id => $this->commentModel->getComments($post->post_id)];
      }
      $data = [
        'posts' => $posts,
        'comments' => $array,
        'recentComment' => '',
        'recentComment_err' => '',
        'pos' => '',
        'post_id' => '',
        'user_id' => ''
      ];

      $this->view('posts/index', $data);

  }
  public function commentPost()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $data = [
        'recentComment' => $_POST['commentContent'],
        'recentComment_err' => '',
        'post_id' => $_POST['postId'],
        'user_id' => $_SESSION['user_id']
      ];

      if (empty($data['recentComment'])) {
        $data['recentComment_err'] = "enter your comment first ;)";
      }

      if (empty($data['recentComment_err'])) {
        // Validated
        if ($this->commentModel->addComment($data)) {
          $comment = $this->commentModel->getLastCommentAdded($data['post_id']);
          echo json_encode($comment);
        } else {
          die('Something went wrong');
        }
      }

    }
  }

  public function add()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $data = [
        'title' => trim($_POST['title']),
        'body' => trim($_POST['body']),
        'image' => $_FILES['image'],
        'user_id' => $_SESSION['user_id'],
        'image_err' => '',
        'title_err' => '',
        'body_err' => ''
      ];

      if (empty($data['title'])) {
        $data['title_err'] = 'Please enter title';
      }
      if (empty($data['body'])) {
        $data['body_err'] = 'Please enter body text';
      }
      
      $imag_name = $data['image']['name'];
      $imag_size = $data['image']['size'];
      $tmp_name = $data['image']['tmp_name'];

      if ($imag_size > 1250000) {
        $data['image_err'] = "sorry , your file is too large ";
      } else {
        $img_ex = pathinfo($imag_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = array("jpg", "jpeg", "png");
        if (in_array($img_ex_lc, $allowed_exs)) {
          $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
          $img_upload_path = "img/$new_img_name";
          move_uploaded_file($tmp_name, $img_upload_path);
          $data['image'] = URLROOT."/img/$new_img_name";
        } else {
          $data['image_err'] = "you can't upload files of this type ";
        }
      }

      if (empty($data['image'])) {
        $data['image_err'] = 'Please select  your image';
      }

      // Make sure no errors
      if (empty($data['title_err']) && empty($data['body_err']) && empty($data['image_err'])) {
        // Validated
        if ($this->postModel->addPost($data)) {
          flash('post_message', 'Post Added');
          redirect('posts');
        } else {
          die('Something went wrong');
        }
      } else {
        // Load view with errors
        $this->view('posts/add', $data);
      }
    } else {
      $data = [
        'title' => '',
        'body' => '',
        'image' => '',
        'user_id' => '',
        'image_err' => '',
        'title_err' => '',
        'body_err' => ''
      ];

      $this->view('posts/add', $data);
    }
  }

  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST array

      $data = [
        'post_id' => $id,
        'title' => trim($_POST['title']),
        'body' => trim($_POST['body']),
        'image' => $_FILES['image'],
        'image_err' => '',
        'title_err' => '',
        'body_err' => ''
      ];

      // Validate data
      if (empty($data['title'])) {
        $data['title_err'] = 'Please enter title';
      }
      if (empty($data['body'])) {
        $data['body_err'] = 'Please enter body text';
      }
      $imag_name = $data['image']['name'];
      $imag_size = $data['image']['size'];
      $tmp_name = $data['image']['tmp_name'];

      if ($imag_size > 1250000) {
        $data['image_err'] = "sorry , your file is too large ";
      } else {
        $img_ex = pathinfo($imag_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = array("jpg", "jpeg", "png");
        if (in_array($img_ex_lc, $allowed_exs)) {
          $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
          $img_upload_path = "img/$new_img_name";
          move_uploaded_file($tmp_name, $img_upload_path);
          $data['image'] = URLROOT."/img/$new_img_name";
        } else {
          $data['image_err'] = "you can't upload files of this type ";
        }
      }

      if (empty($data['image'])) {
        $data['image_err'] = 'Please select  your image';
      }

      // Make sure no errors
      if (empty($data['title_err']) && empty($data['body_err']) && empty($data['image_err'])) {
        // Validated
        if ($this->postModel->updatePost($data)) {
          flash('post_message', 'Post Updated');
          redirect('posts');
        } else {
          die('Something went wrong');
        }
      } else {
        // Load view with errors
        $this->view('posts/edit', $data);
      }
    } else {
      // Get existing post from model
      $post = $this->postModel->getPostById($id);

      // Check for owner
      if ($post->user_id != $_SESSION['user_id']) {
        redirect('posts');
      }

      $data = [
        'post_id' => $id,
        'title' => $post->title,
        'body' => $post->post_body,
        'image' => $post->image,
        'image_err' => '',
        'title_err' => '',
        'body_err' => ''
      ];

      $this->view('posts/edit', $data);
    }
  }

  public function delete($id)
  {
    
      // Get existing post from model
      $post = $this->postModel->getPostById($id);

      // Check for owner
      if ($post->user_id != $_SESSION['user_id']) {
        redirect('posts');
      }

      if ($this->postModel->deletePost($id)) {
        flash('post_message', 'Post Removed');
        redirect('posts');
      } else {
        die('Something went wrong');
      }
    
  }


  public function likePost($id)
  {
    
    
    $data = [
      'post_id' => $id,
      'user_id' => $_SESSION['user_id'],
      'ifExist' => ''
    ];
    $number = $this->postModel->verifyUserIfLikesPost($data['user_id'],$data['post_id']);
    $data['ifExist'] = $number->number;

      if ($this->postModel->like($data)) {
        $post = $this->postModel->getPostById($data['post_id']);
        echo json_encode($post->likes);
      } else {
        die('Something went wrong');
      }

  }



}
