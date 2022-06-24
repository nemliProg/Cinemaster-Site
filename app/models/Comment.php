<?php
  class Comment{
    private $db;
    public function __construct()
    {
      $this->db = new Database;
    }
    

    public function getComments($post_id){
      $this->db->query('SELECT   c.*,u.first_name,u.last_name 
                        FROM     comments c, posts p, users u 
                        WHERE    c.user_id = u.user_id
                        and      c.post_id = p.post_id
                        and      p.post_id = :post_id
                        ORDER By c.published_at DESC');
      $this->db->bind(':post_id', $post_id);                
      $results = $this->db->resultSet();
      return $results;
    }

    public function addComment($data){
      $this->db->query('INSERT INTO comments (comment_body,post_id,user_id) VALUES (:cmnt_body,:post_id,:user_id)');
      $this->db->bind(':cmnt_body', $data['recentComment']);
      $this->db->bind(':post_id', $data['post_id']); 
      $this->db->bind(':user_id', $data['user_id']);               
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    public function getLastCommentAdded($post_id)
    {
      $this->db->query('SELECT   c.*,u.first_name,u.last_name,p.comments 
                        FROM     comments c, posts p, users u 
                        WHERE    c.user_id = u.user_id
                        and      c.post_id = p.post_id
                        and      p.post_id = :post_id
                        ORDER By c.published_at DESC LIMIT 1');
      $this->db->bind(':post_id', $post_id);            
      $results = $this->db->single();
      return $results;
    }


  }
?>