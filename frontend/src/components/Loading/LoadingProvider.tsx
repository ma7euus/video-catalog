import * as React from 'react';
import {useState, useMemo, useEffect} from 'react';
import LoadingContext from './LoadigContext';
import {
    addGlobalRequestInterceptor,
    addGlobalResponseInterceptor,
    removeGlobalRequestInterceptor,
    removeGlobalResponseInterceptor
} from '../../util/http';
import {omit} from 'lodash';

export const LoadingProvider = (props) => {
    const [loading, setLoading] = useState<boolean>(false);
    const [countRequest, setCountRequest] = useState(0);

    useMemo(() => {
        let isSubscribed = true;
        const requestIds = addGlobalRequestInterceptor((config) => {
            if (isSubscribed && !config.headers.hasOwnProperty('ignoreLoading')) {
                setLoading(true);
                setCountRequest((prevCountRequest) => prevCountRequest + 1);
            }
            config.headers = omit(config.headers, 'ignoreLoading');
            return config;
        });

        const responseIds = addGlobalResponseInterceptor((response) => {
            if (isSubscribed) {
                decrementCountRequest();
            }
            return response;
        }, (error) => {
            if (isSubscribed) {
                decrementCountRequest();
            }
            return Promise.reject(error);
        })

        return () => {
            isSubscribed = false;
            removeGlobalRequestInterceptor(requestIds);
            removeGlobalResponseInterceptor(responseIds);
        }
    }, []);

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