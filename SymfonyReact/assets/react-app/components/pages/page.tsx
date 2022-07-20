import * as React from 'react';

export default function Page(props) {
    const content = props.content;
    return(
        <div className="bg-gradient-primary">
            <div className="row justify-content-center" style={{height:'100vh'}}>
                <div className="col-xl-5 col-lg-2 col-md-12">
                    {/*<div className="card o-hidden border-0 shadow-lg my-5">*/}
                        {/*<div className="card-body p-0">*/}
                            {content}
                        {/*</div>*/}
                    {/*</div>*/}
                </div>
            </div>
        </div>
    );
}
