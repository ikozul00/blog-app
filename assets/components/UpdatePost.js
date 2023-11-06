import React, {useState} from "react";
import axios from "axios";
import { useLoaderData, useNavigate} from "react-router-dom";

export const UpdatePost= () => {
    const {post, imageUrl} = useLoaderData();

    const [title, setTitle] = useState(post?.title || "");
    const [image, setImage] = useState(post?.image || "");
    const [content, setContent] = useState(post?.content || "");
    const [error, setError] = useState("");

    const navigate = useNavigate();


    const handleTitle = (e) => {
        setTitle(e.target.value);
    };

    const handleContent = (e) => {
        setContent(e.target.value);
    };

    const handleImageUpload =  (e) => {
        setImage(e.target.files[0]);

    }


    const handleSubmit = async (e) =>{
        e.preventDefault();
        if(title==="" || content==="" ){
            setError("Please enter all the fields");
            return;
        }

        try {
            const formData = new FormData();
            formData.append('id', post.postId);
            formData.append('title', title);
            formData.append('content', content);
            formData.append('image', image);

            const response = await axios.post("/api/posts/update", formData);
            if(response.status===200){
                navigate(`/post/en/${post.postId}`);
            }
            console.log(response);
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
                       value={title} type="text" /><br/>

                <label className="label">Content</label>
                <textarea onChange={handleContent}
                          value={content}  ></textarea><br/>
                { imageUrl!==""  && <div>
                <p>Current image</p>
                    <img src={imageUrl} alt={"post image"}/>
                </div>}
                <label className="label">Image</label>
                <input
                    type="file"
                    name="myImage"
                    onChange={handleImageUpload}/>

                {error && <p>{error}</p>}
                <button onClick={handleSubmit}
                        type="submit">
                    Submit
                </button>
            </form>
        </div>
    )
}