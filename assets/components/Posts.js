import React, {useEffect, useState} from "react";
import ReactDOM from 'react-dom';
import {Link} from "react-router-dom";
import {useNavigate} from "react-router-dom";

export const Posts = ({}) => {
    const [posts, setPosts] = useState([]);

    const user = JSON.parse(localStorage.getItem('user'));
    const navigation = useNavigate();

    useEffect( () => {
        const getData = async () =>{
            const data = await fetch("/api/posts");
            const jsonData= await data.json();
            setPosts(jsonData);
        }
        getData();

    }, []);




    const handleAddPost = () => {
        navigation("/posts/addPost")
    }

    return (
        <>
            <h1>Posts</h1>
            <div>
            {posts.map(post => (
                <div key={post.id}>
                    <Link to={`/posts/${post.id}`} >{post.title}</Link>
                </div>
            ))}
            </div>
            {user && user.role==="admin" && <button onClick={handleAddPost}>Add new</button>}
        </>
    )
}
