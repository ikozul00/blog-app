import React, {useState} from "react";
import axios from "axios";
import {redirect, useNavigate} from "react-router-dom";
import {EditProfileForm} from "./EditProfileForm";

export const Registration= () => {
    const navigate = useNavigate();


    const handleData = async (user) =>{
        const response =await  axios.post("/api/registration", {'email': user.email, 'password':user.password, 'username':user.username});
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