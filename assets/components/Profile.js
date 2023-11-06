import React, {useState} from "react";
import axios from "axios";
import {Link, useLoaderData, useNavigate, useNavigation, useParams} from "react-router-dom";
import {formatDate} from "./helperFunctions";
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
    const params = useParams();
    const {user, favorites, comments, likes} = useLoaderData();

    const [isEditable, setIsEditable] = useState(false);
    const [email, setEmail] = useState(user.email);
    const [username, setUsername] = useState(user.username);
    const [image, setImage] = useState(user.image);
    const [commentsList, setCommentsList] = useState(comments);

    const handleLogout = async() => {
        const response = await axios.get("/api/logout");
        localStorage.clear();
        navigate("/");
    }

    const handleUpdating = async (userData) => {
        try {
            const formData = new FormData();
            formData.append('email', userData.email);
            formData.append('password', userData.password);
            formData.append('oldPassword', userData.oldPassword);
            formData.append('username', userData.username);
            formData.append('image', userData.image);
            try {
                const response = await axios.post(`/api/profile/${userId}`, formData);

            }
            catch(error)
            {
                if (error.response) {
                    console.log(error.response);
                    if(error.response.status===400){
                        if (error.response.data === 'Wrong password.') {
                            userData.setError("Problem while updating password");
                            return;
                        }
                        if (error.response.data === "User exists.") {
                            userData.setError("User with this email already exists.");
                            return;
                        }
                    }
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log('Error', error.message);
                }
            }
            const responseUser = await axios.get(`/api/profile/${params.userId}`);
            setIsEditable(false);
            setEmail(responseUser.data.user.email);
            setUsername(responseUser.data.user.username);
            setImage(responseUser.data.user.image);
        }
        catch(error){
            if (error.response) {
                userData.setError(error.response.data);
            } else if (error.request) {
                userData.setError(error.request.data);
            } else {
                userData.setError('Error', error.message);
            }
        }
    }

    const handleFormVisibility = () => {
        setIsEditable(true);
    }

    const handleDeleteProfile = async () => {
        try {
            const response = await axios.delete(`/api/profile/${userId}`);
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

    const handleDeleteComment =  async (id) => {
        try {
            const response = await axios.delete(`/api/comment/${id}`);
            const newComments = comments.filter(comment => comment.commentId!==id);
            setCommentsList(newComments);
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


    return(
        <div>
            <button onClick={handleLogout}>Logout</button>
            <h2>Profile</h2>
            { loggedUser.role==="admin" && <button onClick={handleDeleteProfile}>Delete</button>}
            {!isEditable && <div>
                <p>Email: {email}</p>
                <p>Username: {username}</p>
                {image!=="" && <img src={image} alt={"user image"}/>}
                <button onClick={handleFormVisibility}>Edit</button>
            </div>}
            {isEditable && ((user.id ==userId) || (loggedUser.role==="admin")) && <EditProfileForm handleData={handleUpdating} emailData={user.email} usernameData={user.username}/>}
            <p>Favorites</p>
            {favorites.map(favorite => <Link to={`/post/en/${favorite.postId}`} key={favorite.postId}>{favorite.title}</Link>)}
            <h4>Activity</h4>
            <h5>Comments</h5>
            {commentsList.map(comment =>
                <div key={comment.commentId}>
                    <hr/>
                    <p>At post: <Link to={`/post/en/${comment.postId}`}>{comment.title}</Link></p>
                    <p>{comment.content}</p>
                    <p>Created at: {formatDate(comment.createdAt.date)}</p>
                    <button onClick={() => handleDeleteComment(comment.commentId)}>Delete comment</button>
                    <hr/>
                </div>)}
            <h5>Likes</h5>
            {likes.map(like =>
                <div key={likes.postId}>
                    <hr/>
                    <p>At post: <Link to={`/post/en/${like.postId}`}>{like.title}</Link></p>
                    <p>Created at: {formatDate(like.timestamp?.date)}</p>
                    <hr/>
                </div>)}

        </div>
    )
}