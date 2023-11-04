import React from "react";
import axios from "axios";
import {Link, useLoaderData, useNavigate, useNavigation} from "react-router-dom";
import {formatDate} from "./Post";

export const loader = async ({params}) => {
    const response = await axios.get(`/api/user/${params.userId}`);
    if(response.status !== 200){
        return{error: "Error while fetching post data."}
    }

    return response.data;
}

export const Profile = () => {
    const navigate=useNavigate();
    const {user, favorites, comments, likes} = useLoaderData();

    const handleLogout = async() => {
        const response = await axios.get("/api/logout");
        localStorage.clear();
        navigate("/");
    }
    console.log(likes);

    return(
        <div>
            <button onClick={handleLogout}>Logout</button>
            <h2>Profile</h2>
            <p>Email: {user.email}</p>
            <p>Username: {user.username}</p>
            <p>Favorites</p>
            {favorites.map(favorite => <Link to={`/posts/${favorite.postId}`}>{favorite.title}</Link>)}
            <h4>Activity</h4>
            <h5>Comments</h5>
            {comments.map(comment =>
                <div>
                    <hr/>
                    <p>At post: <Link to={`/posts/${comment.postId}`}>{comment.title}</Link></p>
                    <p>{comment.content}</p>
                    <p>Created at: {formatDate(comment.createdAt.date)}</p>
                    <hr/>
                </div>)}
            <h5>Likes</h5>
            {likes.map(like =>
                <div key={likes.postId}>
                    <hr/>
                    <p>At post: <Link to={`/posts/${like.postId}`}>{like.title}</Link></p>
                    <p>Created at: {formatDate(like.timestamp?.date)}</p>
                    <hr/>
                </div>)}

        </div>
    )
}