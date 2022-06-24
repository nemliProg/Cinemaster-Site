
let submitComment = document.querySelectorAll('.submitcomment');



function likePost(id){
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST","http://localhost/cinemaster/posts/likePost/"+id,true);
    xmlhttp.onload = function() {
      if ( this.status == 200) {
        document.getElementById('like'+id).textContent = this.responseText;
      }
    }; 
    xmlhttp.send();
};

submitComment.forEach(frm => {
  frm.addEventListener('submit',(e)=>{
    e.preventDefault();
    let postId = e.target.firstElementChild.value;
    let commentContent = e.target.firstElementChild.nextElementSibling.firstElementChild;
    let commentSection = document.getElementById(`comments${postId}`);
    let params = `postId=${postId}&commentContent=${commentContent.value}`;
    let commentNumber = document.getElementById(`cn${postId}`);
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST","http://localhost/cinemaster/posts/commentPost",true);
    xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');

    xmlhttp.onload = function() {
      if ( this.status == 200) {
        let jsonResponse = JSON.parse(this.responseText);
        console.log(jsonResponse)
        //building the structure of the new comment
        let commentContainer = document.createElement('div');
        commentContainer.classList.add('comment');
        let commentBody = document.createElement('p');
        commentBody.appendChild(document.createTextNode(jsonResponse.comment_body));
        let infoDiv = document.createElement('div');
        infoDiv.classList.add('d-flex','justify-content-between');
        let nameSpan = document.createElement('span');
        nameSpan.classList.add('date','d-block');
        nameSpan.appendChild(document.createTextNode(`${jsonResponse.first_name} ${jsonResponse.last_name}`));
        let dateSpan = document.createElement('span');
        dateSpan.classList.add('date','d-block','ms-auto');
        dateSpan.appendChild(document.createTextNode(`${jsonResponse.published_at}`));
        infoDiv.append(nameSpan,dateSpan);
        commentContainer.append(commentBody,infoDiv);
        //end
        commentNumber.textContent = jsonResponse.comments;
        commentContent.value = "";
        commentSection.insertBefore(commentContainer,commentSection.firstElementChild);
      }
    }; 
    xmlhttp.send(params);
  })
});




