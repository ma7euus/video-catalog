import * as React from 'react';
import {useState, useMemo, useEffect} from 'react';
import LoadingContext from './LoadigContext';
import {
    addGlobalRequestInterceptor,
    addGlobalResponseInterceptor,
    removeGlobalRequestInterceptor,
    removeGlobalResponseInterceptor
} from '../../util/http';

export const LoadingProvider = (props) => {
    const [loading, setLoading] = useState<boolean>(false);
    const [countRequest, setCountRequest] = useState(0);

    useMemo(() => {
        let isSubscribed = true;
        const requestIds = addGlobalRequestInterceptor((config) => {
            if (isSubscribed && !config.headers.hasOwnProperty('x-ignore-loading')) {
                setLoading(true);
                setCountRequest((prevCountRequest) => prevCountRequest + 1);
            }
            return config;
        });

        const responseIds = addGlobalResponseInterceptor((response) => {
            if (isSubscribed && !response.config.headers.hasOwnProperty('x-ignore-loading')) {
                decrementCountRequest();
            }
            return response;
        }, (error) => {
            if (isSubscribed && !error.config.headers.hasOwnProperty('x-ignore-loading')) {
                decrementCountRequest();
            }
            return Promise.reject(error);
        });

        return () => {
            isSubscribed = false;
            removeGlobalRequestInterceptor(requestIds);
            removeGlobalResponseInterceptor(responseIds);
        }
        // eslint-disable-next-line
    }, [true]);

    useEffect(() => {
        if (!countRequest) {
            setLoading(false);
        }
    }, [countRequest]);

    function decrementCountRequest() {
        setCountRequest(
            (prevCountRequest) =>
                prevCountRequest <= 0 ? 0 : prevCountRequest - 1
        );
    }

    return (
        <LoadingContext.Provider value={loading}>
            {props.children}
        </LoadingContext.Provider>
    )
}