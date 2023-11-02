import React, {useEffect, useState} from "react";

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
    return (
        <>
        {posts.map(post => (
            <div key={post.id}>
                <p >{post.title}</p>
            </div>
        ))}
        </>
    )
}