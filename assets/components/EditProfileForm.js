import React, {useState} from "react";

export const EditProfileForm= ({emailData="", usernameData="", passwordData="", handleData}) => {
    const [email, setEmail] = useState(emailData);
    const [username, setUsername] = useState(usernameData);
    const [oldPassword, setOldPassword] = useState("");
    const [password, setPassword] = useState(passwordData);
    const [repeatPassword, setRepeatPassword] = useState("");
    const [image, setImage] = useState(null);
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

    const handleOldPassword = (e) => {
        setOldPassword(e.target.value);
    };

    const handleRepeatPassword = (e) => {
        setRepeatPassword(e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if((email==="" || password==="" || repeatPassword==="") && emailData===""){
            setError("Please enter all the fields");
            return;
        }
        if(password!==repeatPassword){
            setError("Passwords do not match.");
            return;
        }
        handleData({email, username, password,image, setError, oldPassword});
    }

    const handleImageUpload =  (e) => {
        setImage(e.target.files[0]);

    }


    return(
        <div>
            <form>
                <label className="label">Email</label>
                <input onChange={handleEmail}
                       value={email} type="email" /><br/>
                <label className="label">Username</label>
                <input onChange={handleUsername}
                       value={username} type="text" /><br/>
                {emailData!== "" && <div>
                <label className="label">Old Password</label>
                <input onChange={handleOldPassword}
                       value={oldPassword} type="password" />
                </div>}
                <label className="label">Password</label>
                <input onChange={handlePassword}
                       value={password} type="password" /><br/>

                <label className="label">Repeat Password</label>
                <input onChange={handleRepeatPassword}
                       value={repeatPassword} type="password" /><br/>
                <label className="label">Image</label>
                <input
                    type="file"
                    name="myImage"
                    onChange={handleImageUpload}/>
                {error && <p>{error}</p>}
                <button onClick={handleSubmit}
                        type="submit">
                    Submit
                </button>
            </form>
        </div>
    )
}