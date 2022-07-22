import * as React from "react";
import * as ReactDOM from "react-dom/client";
import {
    BrowserRouter,
    Routes,
    Route,
} from "react-router-dom";

import Login from "../Routes/Login/Login";

const root = ReactDOM.createRoot(document.getElementById("root"));

root.render(
    <BrowserRouter>
        <Routes>
            <Route path="/HomeApp/WebApp/"></Route>
                <Route path="/HomeApp/WebApp/login" element={<Login />}></Route>
        </Routes>
    </BrowserRouter>
);
