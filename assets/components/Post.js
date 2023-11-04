import React, {useState} from "react";
import axios from "axios";
import {useLoaderData, useNavigate} from "react-router-dom";


export const loader = async ({params}) => {
    const postData = await axios.get(`/api/posts/${params.postId}`);
    if(postData.status !== 200){
        return{error: "Error while fetching post data."}
    }

    return postData.data;
}

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}


export const Post = ()  => {
    const {post, isFavoriteValue, likes, tags} = useLoaderData();

    const [likeNumber, setLikeNumber] = useState(likes);
    const [isFavorite, setIsFavorite] = useState(isFavoriteValue);

    const user = JSON.parse(localStorage.getItem('user'));
    const navigation = useNavigate();

    const handleLikeClick = async () => {
        const response = await axios.post(`/api/likes`, {postId:post.postId});
        if(response.status!==200){
            console.log("Error while adding like.");
            return 0;
        }
        setLikeNumber(likeNumber+1);
    }

    const handleFavoriteClick = async () => {
        if(isFavorite) {
            const response = await axios.delete(`/api/removeFromFavorites/${post.postId}`);
            if (response.status !== 200) {
                console.log("Error while removing from favorites.");
                return 0;
            }
            setIsFavorite(false);
            return 0;
        }
        const response = await axios.post(`/api/addToFavorites`, {'postId':post.postId});
        if (response.status !== 200) {
            console.log("Error while adding to favorites.");
            return 0;
        }
        setIsFavorite(true);
    }

    const handleDeletePost = async () => {
        try {
            const response = await axios.delete(`/api/posts/delete/${post.postId}`);
            navigation("/");
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

    const handleUpdatePost = () =>{
        navigation(`/posts/updatePost/${post.postId}`)
    }

    return(
        <div>
            <h2>{post?.title}</h2>
            <p>{post?.content}</p>
            <p>by {post?.username}</p>
            <p>Created: {formatDate(post?.createdAt.date)}</p>
            {
                post?.lastEdited && <p>Edited: {formatDate(post?.lastEdited.date)}</p>
            }
            {tags.length!==0 &&<div>
                 <p>Tags:</p>
                {
                    tags?.map(tag => <span key={tag.id}>{tag.name}</span>
                    )
                }
            </div>
            }
            <div>
                <span>Likes: {likeNumber} </span>
                {user && <button onClick={handleLikeClick}>Add like</button>}
            </div>
            {user && <button onClick={handleFavoriteClick}>{isFavorite ? "unfavorite" : "favorite"}</button>}
            <br/>
            {user && <button onClick={handleDeletePost}> Delete Post</button>}
            <br/>
            {user && <button onClick={handleUpdatePost}> Update Post</button>}


        </div>
    )
}