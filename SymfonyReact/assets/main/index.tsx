import * as React from "react";
import * as ReactDOM from "react-dom";
import {
    BrowserRouter as Router,
    Switch,
    Route,
    Link
} from "react-router-dom";


export default function App() {
    return (
        <Router>
            <Route exact path-"/HomeApp/WebApp/login"></Route>
        </Router>
    );
}
ReactDOM.render(<App/>, document.getElementById("root"));
