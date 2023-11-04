import React, {useState} from "react";
import axios from "axios";
import {redirect} from "react-router-dom";

export const Registration= () => {
    const [email, setEmail] = useState("");
    const [username, setUsername] = useState("");
    const [password, setPassword] = useState("");
    const [repeatPassword, setRepeatPassword] = useState("");
    const [error, setError] = useState("");

    const handleEmail = (e) => {
        setEmail(e.target.value);
    };

    const handleUsername = (e) => {
        setUsername(e.target.value);
    };
    const handlePassword = (e) => {
        setPassword(e.target.value);
    };

    const handleRepeatPassword = (e) => {
        setRepeatPassword(e.target.value);
    };

    const handleSubmit = async (e) =>{
        e.preventDefault();
        if(email==="" || password==="" || repeatPassword===""){
            setError("Please enter all the fields");
            return;
        }
        if(password!==repeatPassword){
            setError("Passwords do not match.");
            return;
        }
        const response =await  axios.post("/api/profile/registration", {'email': email, 'password':password});
        console.log(response);
        if(response.data==="User exists."){
            setError("User with this email already exists.");
            return;
        }
        redirect("/");
    }

    return(
        <div>
            <form>
                <label className="label">Email</label>
                <input onChange={handleEmail}
                       value={email} type="email" />
                <label className="label">Username</label>
                <input onChange={handleUsername}
                       value={username} type="text" />
                <label className="label">Password</label>
                <input onChange={handlePassword}
                       value={password} type="password" />

                <label className="label">Repeat Password</label>
                <input onChange={handleRepeatPassword}
                       value={repeatPassword} type="password" />
                {error && <p>{error}</p>}
                <button onClick={handleSubmit}
                        type="submit">
                    Submit
                </button>
            </form>
        </div>
    )
}