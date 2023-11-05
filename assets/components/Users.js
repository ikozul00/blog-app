import React from "react";
import axios from "axios";
import {Link, useLoaderData} from "react-router-dom";

export const loader = async ({params}) => {
    const response = await axios.get(`/api/profiles`);
    if(response.status !== 200){
        return{error: "Error while fetching post data."}
    }

    return response.data;
}

const UserItem = ({email, id, username}) => {
    return(
        <Link to={`/profile/${id}`}>
            <p>Email: {email}</p>
            <p>Username: {username}</p>
            <hr/>
        </Link>
    )
}

export const Users = () => {
    const users = useLoaderData();

    return(
        <div>
            {users.map(user => <UserItem email={user.email} username={user.username} id={user.id} key={user.id}/>)}
        </div>)

}