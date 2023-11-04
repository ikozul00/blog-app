import React from "react";
import axios from "axios";
import {useNavigate, useNavigation} from "react-router-dom";

export const Profile = () => {
    const navigate=useNavigate();
    const handleLogout = async() => {
        const response = await axios.get("/api/profile/logout");
        navigate("/");
    }

    return(
        <button onClick={handleLogout}>Logout</button>
    )
}