import React from 'react';

export const schedulableSessionStyle = {
    width: '260px',
    margin: '4px 4px 6px 4px',
    border: '1px solid black',
    padding: '4px',
    fontSize: '0.9rem'
};

export const hiddenSessionStyle: React.CSSProperties = {
    ...schedulableSessionStyle,
    visibility: 'hidden' /* as React.CSSProperties.Visibility */
}
