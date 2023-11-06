import React, {useState} from "react";
import axios from "axios";
import { useNavigate} from "react-router-dom";
import {EditProfileForm} from "./EditProfileForm";

export const Registration= () => {
    const navigate = useNavigate();


    const handleData = async (user) =>{
        const formData = new FormData();
        formData.append('email', user.email);
        formData.append('password', user.password);
        formData.append('username', user.username);
        formData.append('image', user.image);
        const response =await  axios.post("/api/registration", formData);
        if(response.data==="User exists."){
            user.setError("User with this email already exists.");
            return;
        }
        navigate("/");
    }

    return(
        <div>
            <EditProfileForm handleData={handleData}/>
        </div>
    )
}