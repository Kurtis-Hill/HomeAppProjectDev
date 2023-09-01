import * as React from 'react';


export function GeneralTableHeaders(props: {headers: string[]|null}) {
    const { headers } = props;
    const filteredHeaders = headers.filter(s => s);
    
    return (
        <thead>
            <tr>
                {
                    filteredHeaders.map((header: string, index: number) => {
                        return (
                            <th scope="col" key={index}>{ header }</th>
                        );
                    })
                }   
            </tr>
        </thead>
    )
}