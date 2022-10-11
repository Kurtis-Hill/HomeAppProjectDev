import * as React from 'react';
import {
    Link,
    Outlet,
  } from "react-router-dom";

import { webappURL } from "../../Common/CommonURLs";

import Navbar from "../Navbar/Navbar";

export function MainPageTop() {
    return (
        <div id="page-top">
            <div id="wrapper">   
                <Navbar />               
                <Outlet />
            </div>
        </div>
    );
}