import * as React from 'react';


export function GeneralTableHeaders(props: {headers: string[]|null}) {
    const { headers } = props;
    const filteredHeaders = headers !== null ? headers.filter(s => s) : null;
    
    return (
        <thead>
            <tr>
                {
                    filteredHeaders !== null 
                        ? filteredHeaders.map((header: string, index: number) => {
                            return (
                                <th scope="col" key={index}>{ header }</th>
                            );
                        })
                        : null
                }   
            </tr>
        </thead>
    )
}