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

    const tagsFunctions = async () => {
        //const data = await fetch("/tags");
        //const data = await fetch("/tags/create",
        //    {method:"POST",
        //        headers: { 'Content-Type': 'application/json' },
        //        body:JSON.stringify({'name':'great'})
        //        });

        //const data = await fetch("/tags/delete/83", {method:"DELETE"});

        //const data = await fetch("/tags/update",
         //       {method:"PUT",
          //          headers: { 'Content-Type': 'application/json' },
          //         body:JSON.stringify({'id': '90','name':'some new name'})
          //          });
        const data = await fetch("/posts/load/22");
        console.log(data);
        const jsonData= await data.json();
        console.log(jsonData);
    }

    const addTagToPost = async () => {
        //const data = await fetch("/posts/addTag",
        //    { method: 'POST',
        //        headers: { 'Content-Type': 'application/json' },
        //        body: JSON.stringify({ 'postId' : '24','tagId':'90'
        //            }
        //        )}
        //);

        const data = await fetch("/posts/removeTag",
            { method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'postId' : '24','tagId':'81'
                    }
                )}
        );
        console.log(data);
        const jsonData= await data.json();
        console.log(jsonData);
    }

    const addLike = async () => {
        const data = await fetch("/likes/24", {
            method:"POST",
            headers: { 'Content-Type': 'application/json' }, body:JSON.stringify({'userId': '62'})});
        console.log(data);
        const jsonData= await data.json();
        console.log(jsonData);

    }

    const addFavorite = async () => {
        //const data = await fetch("/addToFavorites/24", {
        //    method:"POST",
        //    headers: { 'Content-Type': 'application/json' }, body:JSON.stringify({'userId': '62'})});
        const data = await fetch("/removeFromFavorites/24/62", {
            method:"DELETE",
           });
        console.log(data);
        const jsonData= await data.json();
        console.log(jsonData);
    }

    return (
        <>
        {posts.map(post => (
            <div key={post.id}>
                <a href={`/posts/load/${post.id}`}>{post.title}</a>
            </div>
        ))}
            <button onClick={addFavorite}>Add</button>
        </>
    )
}
