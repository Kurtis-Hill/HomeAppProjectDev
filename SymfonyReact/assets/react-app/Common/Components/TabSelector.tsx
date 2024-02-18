import * as React from 'react';


export function TabSelector(props: {
    options: string[],
    currentTab: string,
    setCurrentTab: (tab: string) => void,
}) {
    const { options, currentTab, setCurrentTab } = props;
    
    return (
        <>
            <div className="btn-group btn-group-toggle" data-toggle="buttons" style={{paddingBottom: '3%'}}>
                {
                    options.map((option: string, index: number) => {
                        return (
                            <label className={`btn btn-secondary ${currentTab === option ? 'active' : null}`} key={index}>
                                <input type="radio" name="options" id={`option${index}`} autoComplete="off" onClick={() => setCurrentTab(option)} /> {option}
                            </label>
                        );
                    })
                }
            </div>
        </>
    );
}