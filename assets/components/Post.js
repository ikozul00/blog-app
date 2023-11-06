import React, {useState, useEffect} from "react";
import axios from "axios";
import {useLoaderData, useNavigate, useParams} from "react-router-dom";
import {Comments} from "./Comments";
import {formatDate} from "./helperFunctions";


export const loader = async ({params}) => {
    const language = params.lang ? params.lang : 'en';
    const labels=await axios.get(`/api/${language}/post/page`);
    const postData = await axios.get(`/api/post/${params.postId}`);
    if(postData.status !== 200){
        return{error: "Error while fetching post data."}
    }

    return {...postData.data, 'labels':labels.data};
}




export const Post = ()  => {
    const {post, isFavoriteValue, likes, tags, comments, imageUrl, labels} = useLoaderData();

    const [likeNumber, setLikeNumber] = useState(likes);
    const [isFavorite, setIsFavorite] = useState(isFavoriteValue);
    const [isDropdownVisible, setIsDropdownVisible] = useState(false);
    const [selectedTag, setSelectedTag] = useState("");
    const [allTags, setAllTags] = useState([]);
    const [postTags, setPostTags] = useState(tags);
    const [error, setError] = useState("");

    const user = JSON.parse(localStorage.getItem('user'));
    const navigation = useNavigate();
    const params = useParams();
    const language = params.lang ? params.lang : 'en';

    useEffect(() => {
        const getTags = async () => {
            const response = await axios.get(`/api/tags`);
            if(response.status !== 200){
                return{error: "Error while fetching tags."}
            }
            setAllTags(response.data);
        }
        getTags();
    }, [])

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
        navigation(`/post/updatePost/${post.postId}`)
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
            <p>Created: {formatDate(post?.createdAt.date, language)}</p>
            {
                post?.lastEdited && <p>Edited: {formatDate(post?.lastEdited.date, language)}</p>
            }

            <div>
                 <p>Tags:</p>
                {
                    postTags?.map(tag => <span key={tag.id}>{tag.name}</span>
                    )
                }
                {!isDropdownVisible && user && user.role==='admin' && <button onClick={handleDropdownVisible}>{labels.tagAdd}</button>}
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
                <span>{labels.like}: {likeNumber} </span>
                {user && <button onClick={handleLikeClick}>{labels.like}</button>}
            </div>
            {user && <button onClick={handleFavoriteClick}>{isFavorite ? labels.favoriteRemove : labels.favorite}</button>}
            <br/>
            {user && user.role==="admin" && <button onClick={handleDeletePost}> {labels.delete}</button>}
            <br/>
            {user && user.role==="admin" && <button onClick={handleUpdatePost}> {labels.update}</button>}
            <div>
                <h3>{labels.comments}</h3>
                <Comments commentsData={comments} postId={post.postId}></Comments>
            </div>



        </div>
    )
}