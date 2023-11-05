import React, {useState} from "react";
import {formatDate} from "./Post";
import axios from "axios";
import {useNavigate} from "react-router-dom";

export const Comments = ({commentsData, postId}) => {
    const [comments, setComments] = useState(commentsData);
    const [isAdding, setIsAdding] = useState(false);
    const [newContent, setNewContent] = useState("");
    const [error, setError] = useState("");

    const user = JSON.parse(localStorage.getItem('user'));

    const handleDelete =  async (id) => {
        try {
            const response = await axios.delete(`/api/comment/delete/${id}`);
            const newComments = comments.filter(comment => comment.commentId!==id);
            setComments(newComments);
        }
        catch(error){
            if (error.response) {
                console.log(error.response);
            } else if (error.request) {
                console.log(error.request);
            } else {
                console.log('Error', error.message);
            }
        }
    }

    const handleContent = (e) => {
        setNewContent(e.target.value);
    };


    const handleSubmit = async (e) =>{
        e.preventDefault();
        if(newContent==="" ){
            setError("Please enter content");
            return;
        }

        try {
            const response = await axios.post("/api/comment/create", {'postId': postId, 'content': newContent});
            setComments([...comments, response.data]);
            setIsAdding(false);
        }
        catch(error){
            if (error.response) {
                setError(error.response.data);
            } else if (error.request) {
                setError(error.request.data);
            } else {
                setError('Error', error.message);
            }
        }

    }

    const handleAddingComment = () =>{
        setIsAdding(true);
    }

    return(
        <div>
            {comments.map(comment =>
                <div key={comment.commentId}>
                    <hr/>
                    <p>{comment?.content}</p>
                    <p>by {comment?.email}</p>
                    <p>created: {formatDate(comment?.createdAt.date)}</p>
                    {user && (user.id == comment.userId || user.role==="admin") &&  <button onClick={() => handleDelete(comment.commentId)}>Delete comment</button>}
                    <hr/>
                </div>
                )
            }
            {!isAdding && user && <button onClick={handleAddingComment}>Add comment</button>}
            {isAdding &&
                    <form>
                        <label className="label">Content</label>
                        <textarea onChange={handleContent}
                               value={newContent} rows={15} cols={15}/>

                        {error && <p>{error}</p>}
                        <button onClick={handleSubmit}
                                type="submit">
                            Submit
                        </button>
                    </form>
            }

        </div>
    )
}