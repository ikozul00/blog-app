import React, {useState} from "react";
import axios from "axios";
import {useLoaderData} from "react-router-dom";
import {Tag} from "./Tag";

export const loader = async () => {
    const response = await axios.get(`/api/tags`);
    if(response.status !== 200){
        return{error: "Error while fetching tags."}
    }
    return response.data;
}

export const Tags = () => {
    const tagsData = useLoaderData();
    const [tags, setTags] = useState(tagsData);
    const [isVisible, setIsVisible] = useState(false);
    const [name, setName] = useState("");
    const [error, setError] = useState("");

    const user = JSON.parse(localStorage.getItem('user'));

    const handleAddTagForm = () => {
        setIsVisible(true);
    }

    const handleAdding = async (e) => {
        e.preventDefault();
        if(name==="" ){
            setError("Please enter name");
            return;
        }

        try {
            const response = await axios.post("/api/tags/create", {'name': name});
            setTags([...tags, response.data]);
            setIsVisible(false);
        }
        catch(error){
            if (error.response) {
                setError(error.response.data);
            } else if (error.request) {
                setError(error.request.data);
            } else {
                setError('Error', error.message);
            }
        }
    }

    const handleName=(e) => {
        setName(e.target.value);
    }

    return(
        <div>
            <hr/>
            {tags.map(tag => <Tag name={tag.name} id={tag.id} tags={tags} setTags={setTags}></Tag>)}
            {!isVisible && user && user.role==='admin' && <button onClick={handleAddTagForm}>Add tag</button>}
            {isVisible &&
                <form>
                    <label className="label">Name</label>
                    <input onChange={handleName}
                              value={name} type={"text"}/>
                    {error && <p>error</p>}
                    <button onClick={handleAdding}
                            type="submit">
                        Add
                    </button>

                </form>}
        </div>
    )

}