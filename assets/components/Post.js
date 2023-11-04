import React from "react";
import axios from "axios";
import {useLoaderData} from "react-router-dom";


export const loader = async ({params}) => {
    const postData = await axios.get(`/api/posts/${params.postId}`);
    if(postData.status !== 200){
        return{error: "Error while fetching post data."}
    }

    return postData.data;
}

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}


export const Post = ()  => {
    const {post, isFavorite, likes, tags} = useLoaderData();

    return(
        <div>
            <h2>{post?.title}</h2>
            <p>{post?.content}</p>
            <p>by {post?.username}</p>
            <p>Created: {formatDate(post?.createdAt.date)}</p>
            {
                post?.lastEdited && <p>Edited: {formatDate(post?.lastEdited.date)}</p>
            }
            {tags.length!==0 &&<div>
                 <p>Tags:</p>
                {
                    tags?.map(tag => <span key={tag.id}>{tag.name}</span>
                    )
                }
            </div>
            }
            <div>
                <span>{likes} </span><button>Like</button>
            </div>
            {isFavorite ? <button>unfavorite</button> : <button>favorite</button>}

        </div>
    )
}