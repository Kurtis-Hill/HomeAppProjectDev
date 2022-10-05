import * as React from 'react';

export default function ColouredPage(props) {
    const content: string = props.content ?? '';
    const backgroundColour: string = props.backgroundColour ?? 'primary'
    const rowData: string = props.rowData ?? 'col-xl-6 col-lg-6 col-md-11'
    
    return (
        <div className={`bg-gradient-${backgroundColour}`}>
            <div className="row justify-content-center" style={{height:'100vh'}}>
                <div className={`${rowData}`}>
                    {content}
                </div>
            </div>
        </div>
    );
}
