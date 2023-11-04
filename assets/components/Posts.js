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

    const handleClick = async () => {
        const data = await fetch("/posts/update/24",
            { method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'title' : 'Really great post',
                                            }
                )}
        );
        const jsonData= await data.json();
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
