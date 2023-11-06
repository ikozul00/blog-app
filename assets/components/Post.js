import React, {useEffect, useState} from "react";
import axios from "axios";
import {useLoaderData, useNavigate} from "react-router-dom";
import {Comments} from "./Comments";


export const loader = async ({params}) => {
    const postData = await axios.get(`/api/post/${params.postId}`);
    if(postData.status !== 200){
        return{error: "Error while fetching post data."}
    }

    return postData.data;
}

export const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}


export const Post = ()  => {
    const {post, isFavoriteValue, likes, tags, comments, imageUrl} = useLoaderData();

    const [likeNumber, setLikeNumber] = useState(likes);
    const [isFavorite, setIsFavorite] = useState(isFavoriteValue);
    const [isDropdownVisible, setIsDropdownVisible] = useState(false);
    const [selectedTag, setSelectedTag] = useState("");
    const [allTags, setAllTags] = useState([]);
    const [postTags, setPostTags] = useState(tags);
    const [error, setError] = useState("");

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

    const handleTagOption = (e) => {
        setSelectedTag(e.target.value);
    }

    const handleAddTagToPost = async () => {
        try {
            const response = await axios.post(`/api/posts/addTag`,
                {'postId': post.postId, 'tagId': selectedTag});
            if(response.data === "Exists."){
                setError("Tag already added.");
                return;
            }
            setPostTags([...postTags, selectedTag]);
            setIsDropdownVisible(false);
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

    const handleDropdownVisible = () => {
        setIsDropdownVisible(true);
    }

    return(
        <div>
            <h2>{post?.title}</h2>
            {imageUrl!==""  && <img src={imageUrl} alt={"post image"}/>}
            <p>{post?.content}</p>
            <p>by {post?.email}</p>
            <p>Created: {formatDate(post?.createdAt.date)}</p>
            {
                post?.lastEdited && <p>Edited: {formatDate(post?.lastEdited.date)}</p>
            }

            <div>
                 <p>Tags:</p>
                {
                    postTags?.map(tag => <span key={tag.id}>{tag.name}</span>
                    )
                }
                {!isDropdownVisible && user && user.role==='admin' && <button onClick={handleDropdownVisible}>Add tag</button>}
                {isDropdownVisible && <div>
                    <select onChange={handleTagOption}  id="tags" value={selectedTag}>
                        {
                            allTags.map(tag => <option value={tag.id} key={tag.id}>{tag.name}</option>)
                        }
                    </select>
                    {error && <p>{error}</p>}
                    <button onClick={handleAddTagToPost}>Choose</button>
                </div>}
            </div>
            <div>
                <span>Likes: {likeNumber} </span>
                {user && <button onClick={handleLikeClick}>Add like</button>}
            </div>
            {user && <button onClick={handleFavoriteClick}>{isFavorite ? "unfavorite" : "favorite"}</button>}
            <br/>
            {user && user.role==="admin" && <button onClick={handleDeletePost}> Delete Post</button>}
            <br/>
            {user && user.role==="admin" && <button onClick={handleUpdatePost}> Update Post</button>}
            <div>
                <h3>Comments</h3>
                <Comments commentsData={comments} postId={post.postId}></Comments>
            </div>



        </div>
    )
}