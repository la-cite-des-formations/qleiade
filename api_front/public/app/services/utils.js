import parse from 'html-react-parser';
import React, { lazy, Suspense } from 'react';

function htmlDecode(input) {
    return parse(input || "");
}

function getComponent(name, properties, canSee) {
    //pass absolute path to resource
    //TODO if not found in registry go to supplies , and if not found in supplies make with default (mycard)
    var Component = lazy(() => import(/* webpackIgnore: true */"@parts/" + name));
    var el = <Suspense fallback={<div>Hi, This component is Loading...</div>}>
        <Component item={properties} canSee={canSee} />
    </Suspense>;

    return el;
}


export { htmlDecode, getComponent };
