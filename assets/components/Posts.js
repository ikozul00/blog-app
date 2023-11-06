import React, {useCallback, useEffect, useState} from "react";
import {Link} from "react-router-dom";
import {useNavigate} from "react-router-dom";
import axios from "axios";

const numberOfItemsPerPage = 10;



export const Posts = ({}) => {
    const [posts, setPosts] = useState([]);
    const [page, setPage] = useState(1);
    const [isLoadMore, setIsLoadMore] = useState(true);

    const user = JSON.parse(localStorage.getItem('user'));
    const navigation = useNavigate();

    const getPosts = useCallback(async () => {
        const response = await axios.get(`/api/posts/?page=${page}&&limit=${numberOfItemsPerPage}`);
        const {postsList, postsCount, currentPageNumber} = response.data;
        setPosts([...posts, ...postsList ]);
        if(currentPageNumber*numberOfItemsPerPage < postsCount){
            setPage(page+1);
        }
        else{
            setIsLoadMore(false);
        }
    }, [page, numberOfItemsPerPage]);

    useEffect( () => {
        const getPosts = async () => {
            const response = await axios.get(`/api/posts/?page=${page}&&limit=${numberOfItemsPerPage}`);
            const {postsList, postsCount, currentPageNumber} = response.data;
            setPosts([...posts, ...postsList ]);
            if(currentPageNumber*numberOfItemsPerPage < postsCount){
                setPage(page+1);
            }
            else{
                setIsLoadMore(false);
            }
        }
        getPosts();
    }, []);

    const loadMorePosts = async () => {
        const response = await axios.get(`/api/posts/?page=${page}&&limit=${numberOfItemsPerPage}`);
        const {postsList, postsCount, currentPageNumber} = response.data;
        setPosts([...posts, ...postsList ]);
        if(currentPageNumber*numberOfItemsPerPage < postsCount){
            setPage(page+1);
        }
        else{
            setIsLoadMore(false);
        }
    }
    

    const handleAddPost = () => {
        navigation("/posts/addPost")
    }

    return (
        <>
            <h1>Posts</h1>
            <div>
            {posts.map(post =>
            {
                console.log(post);
                return(
                <div key={post.id}>
                    <Link to={`/posts/${post.id}`} >{post.title}</Link>
                </div>
            )})}
            </div>
            {isLoadMore && <button onClick={loadMorePosts}>Load More</button>}<br/>
            {user && user.role==="admin" && <button onClick={handleAddPost}>Add new</button>}
        </>
    )
}
