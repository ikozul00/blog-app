import React, {useState} from "react";
import axios from "axios";

export const Tag = ({id, name, tags, setTags}) => {
    const [isVisible, setIsVisible] = useState(false);
    const [newName, setNewName] = useState(name);
    const [error, setError] = useState("");

    const user = JSON.parse(localStorage.getItem('user'));

    const handleDelete = async () => {
        try {
            const response = await axios.delete(`/api/tag/${id}`);
            const newTags = tags.filter(tag => tag.id!==id);
            setTags(newTags);
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

    const handleName=(e) => {
        setNewName(e.target.value);
    }

    const handleUpdate = () => {
        setIsVisible(true);
    }

    const handleSaveChange = async (e) => {
        e.preventDefault();
        if(newName==="" ){
            setError("Please enter name");
            return;
        }

        try {
            const response = await axios.put("/api/tag", {'name': newName, 'id':id});
            const newTags = tags.filter(tag => tag.id!== id);
            setTags([...newTags, {id:id, name: newName}]);
            setIsVisible(false);
        }
        catch(error){
            if (error.response) {
                    setError(error.response.data.message);

            } else if (error.request) {
                setError(error.request.data);
            } else {
                setError('Error', error.message);
            }
        }
    }

    return(
        <div>
            {!isVisible && <div>
            <p>{name}</p>
            {user && user.role ==='admin' && <button onClick={handleDelete}>Delete</button>}
            {user  && !isVisible && user.role === 'admin' && <button onClick={handleUpdate}>Update</button>}
            </div>}
            {isVisible &&
                <form>
                    <label className="label">Name</label>
                    <input onChange={handleName}
                           value={newName} type={"text"}/>
                    {error && <p>{error}</p>}
                    <button onClick={handleSaveChange}
                            type="submit">
                        Save
                    </button>
                </form>

            }
            <hr/>
        </div>
    );
}