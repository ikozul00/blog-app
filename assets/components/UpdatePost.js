import React, {useState} from "react";
import axios from "axios";
import { useLoaderData, useNavigate} from "react-router-dom";

export const UpdatePost= () => {
    const {post} = useLoaderData();

    const [title, setTitle] = useState(post?.title || "");
    const [content, setContent] = useState(post?.content || "");
    const [error, setError] = useState("");

    const navigate = useNavigate();


    const handleTitle = (e) => {
        setTitle(e.target.value);
    };

    const handleContent = (e) => {
        setContent(e.target.value);
    };


    const handleSubmit = async (e) =>{
        e.preventDefault();
        if(title==="" || content==="" ){
            setError("Please enter all the fields");
            return;
        }

        try {
            const response = await axios.put("/api/posts/update", {'id': post.postId, 'title': title, 'content': content});
            if(response.status===200){
                navigate(`/posts/${post.postId}`);
            }
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


    return(
        <div>
            <h2>Update Post</h2>
            <form>
                <label className="label">Title</label>
                <input onChange={handleTitle}
                       value={title} type="text" />

                <label className="label">Content</label>
                <textarea onChange={handleContent}
                          value={content}  ></textarea>

                {error && <p>{error}</p>}
                <button onClick={handleSubmit}
                        type="submit">
                    Submit
                </button>
            </form>
        </div>
    )
}