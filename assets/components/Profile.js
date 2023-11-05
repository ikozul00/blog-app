import React, {useState} from "react";
import axios from "axios";
import {Link, useLoaderData, useNavigate, useNavigation, useParams} from "react-router-dom";
import {formatDate} from "./Post";
import {EditProfileForm} from "./EditProfileForm";

export const loader = async ({params}) => {
    const response = await axios.get(`/api/profile/${params.userId}`);
    if(response.status !== 200){
        return{error: "Error while fetching post data."}
    }

    return response.data;
}

export const Profile = () => {
    const navigate=useNavigate();
    let {userId} = useParams();
    const loggedUser = JSON.parse(localStorage.getItem('user'));
    const {user, favorites, comments, likes} = useLoaderData();

    const [isEditable, setIsEditable] = useState(false);
    const [email, setEmail] = useState(user.email);
    const [username, setUsername] = useState(user.username);

    const handleLogout = async() => {
        const response = await axios.get("/api/logout");
        localStorage.clear();
        navigate("/");
    }

    const handleUpdating = async (userData) => {
        try {
            const response = await axios.put(`/api/profile/update/${userId}`,
                {'email': userData.email, 'password': userData.password, 'username': userData.username, 'oldPassword':userData.oldPassword});
            if(response.data === 'Wrong password.'){
                userData.setError("Problem while updating password");
                return;
            }
            if(response.data==="User exists."){
                userData.setError("User with this email already exists.");
                return;
            }
            setIsEditable(false);
            setEmail(userData.email);
            setUsername(userData.username);
        }
        catch(error){
            if (error.response) {
                user.setError(error.response.data);
            } else if (error.request) {
                user.setError(error.request.data);
            } else {
                user.setError('Error', error.message);
            }
        }
    }

    const handleFormVisibility = () => {
        setIsEditable(true);
    }

    const handleDeleteProfile = async () => {
        try {
            const response = await axios.delete(`/api/profile/delete/${userId}`);
            navigate("/profiles");
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

    console.log((user.id ===userId) || (user.role==="admin"));
    return(
        <div>
            <button onClick={handleLogout}>Logout</button>
            <h2>Profile</h2>
            { loggedUser.role==="admin" && <button onClick={handleDeleteProfile}>Delete</button>}
            {!isEditable && <div>
                <p>Email: {email}</p>
                <p>Username: {username}</p>
                <button onClick={handleFormVisibility}>Edit</button>
            </div>}
            {isEditable && ((user.id ===userId) || (loggedUser.role==="admin")) && <EditProfileForm handleData={handleUpdating} emailData={user.email} usernameData={user.username}/>}
            <p>Favorites</p>
            {favorites.map(favorite => <Link to={`/posts/${favorite.postId}`} key={favorite.postId}>{favorite.title}</Link>)}
            <h4>Activity</h4>
            <h5>Comments</h5>
            {comments.map(comment =>
                <div key={comment.commentId}>
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