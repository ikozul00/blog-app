import React, {useState} from "react";
import axios from "axios";
import { useNavigate} from "react-router-dom";

export const NewPost= () => {
    const [title, setTitle] = useState("");
    const [content, setContent] = useState("");
    const [image, setImage] = useState(null);
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
            const formData = new FormData();
            formData.append('title', title);
            formData.append('content', content);
            formData.append('image', image);
            const response = await axios.post("/api/posts/create", formData);
            if(response.status===200){
                navigate(`/posts/${response.data.id}`);
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

    const handleImageUpload =  (e) => {
        setImage(e.target.files[0]);

    }


    return(
        <div>
            <h2>New Post</h2>
            <form>
                <label className="label">Title</label>
                <input onChange={handleTitle}
                       value={title} type="text" /><br/>

                <label className="label">Content</label>
                <textarea onChange={handleContent}
                          value={content}  ></textarea><br/>
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