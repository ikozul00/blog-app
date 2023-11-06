import React, {useCallback, useEffect, useState} from "react";
import {Link} from "react-router-dom";
import {useNavigate} from "react-router-dom";
import axios from "axios";
import {formatDate} from "./Post";

const numberOfItemsPerPage = 10;



export const Posts = ({}) => {
    const [posts, setPosts] = useState([]);
    const [page, setPage] = useState(1);
    const [filter, setFilter] = useState("");
    const [isLoadMore, setIsLoadMore] = useState(true);

    const user = JSON.parse(localStorage.getItem('user'));
    const navigation = useNavigate();


    useEffect( () => {
        const getPosts = async () => {
            const response = await axios.get(`/api/posts/?page=${page}&&limit=${numberOfItemsPerPage}`);
            const {postsList, postsCount, currentPageNumber} = response.data;
            setPosts([...posts, ...postsList ]);
            if(currentPageNumber*numberOfItemsPerPage < postsCount){
                setPage(currentPageNumber+1);
            }
            else{
                setIsLoadMore(false);
            }
        }
        getPosts();
    }, []);

    const loadMorePosts = async (initialPosts= posts, initialPage = page) => {
        const requestUrl= filter !=="" ?
            `/api/posts/?page=${initialPage}&&limit=${numberOfItemsPerPage}&&filter=${filter}` :
            `/api/posts/?page=${initialPage}&&limit=${numberOfItemsPerPage}`;
        const response  = await axios.get(requestUrl);
        const {postsList, postsCount, currentPageNumber} = response.data;
        setPosts([...initialPosts, ...postsList ]);
        if(currentPageNumber*numberOfItemsPerPage < postsCount){
            setPage(currentPageNumber+1);
            setIsLoadMore(true);
        }
        else{
            setIsLoadMore(false);
        }
    }


    const handleAddPost = () => {
        navigation("/posts/addPost")
    }

    const handleSearch = () => {
        loadMorePosts([], 1);
    }

    return (
        <>
            <h1>Posts</h1>
            <input type={"text"} value={filter} name={"filter"} id={"filter"}
                   onChange={(e) => setFilter(e.target.value)}/>
            <button onClick={handleSearch}>Search</button>
            <div>
            {posts.map(post =>
            {
                return(
                <div key={post.id}>
                    <Link to={`/posts/${post.id}`} >{post.title}</Link>
                    <span>{formatDate(post.createdAt.date)}</span>
                </div>
            )})}
            </div>
            {isLoadMore && <button onClick={() => loadMorePosts()}>Load More</button>}<br/>
            {user && user.role==="admin" && <button onClick={handleAddPost}>Add new</button>}
        </>
    )
}
