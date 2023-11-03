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
        const data = await fetch("/posts/update/24",
            { method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'title' : 'Really great post',
                                            }
                )}
        );
        console.log(data);
        const jsonData= await data.json();
        console.log(jsonData);
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
