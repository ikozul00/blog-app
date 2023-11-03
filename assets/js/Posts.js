import React, {useEffect, useState} from "react";
import ReactDOM from 'react-dom';

export const Posts = ({}) => {
    const [posts, setPosts] = useState([]);
    useEffect( () => {
        const getData = async () =>{
            const data = await fetch("/posts");
            const jsonData= await data.json();
            setPosts(jsonData);
        }
        getData();


    }, []);

    const handleClick = async () => {
        const data = await fetch("/posts/delete/23", { method: 'DELETE' });
        console.log(data);
    }

    return (
        <>
        {posts.map(post => (
            <div key={post.id}>
                <a href={`/posts/${post.id}`}>{post.title}</a>
            </div>
        ))}
            <button onClick={handleClick}>Add</button>
        </>
    )
}
