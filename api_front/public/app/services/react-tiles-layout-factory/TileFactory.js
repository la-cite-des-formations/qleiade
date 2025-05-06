import React, { lazy, Suspense } from "react";

const tileModels = [
    {
        type: 'MyCard',
        minh: 8,
        minw: 3
    },
    {
        type: 'Page',
        minh: 8,
        minw: 3
    },
    {
        type: 'Hello',
        minh: 8,
        minw: 3
    },
    {
        type: 'QualityLabel',
        minh: 16,
        minw: 3
    },
    {
        type: 'Unit',
        x: 8,
        y: 0,
        minh: 8,
        minw: 3
    },
    {
        type: 'QualityLabelPresenter',
        minh: 16,
        minw: 3
    },
    {
        type: 'AuditPresenter',
        minh: 25,
        minw: 3
    },
    {
        type: 'Container',
        minh: 10,
        minw: 3
    },


]
//je veux un layout différent en fonction du type qualityLabels, critere....
export default class TileFactory {
    constructor(purchaseOrder) {
        this.purchaseOrder = purchaseOrder;
    }

    getComponent(name, elements) {
        //if not found in registry go to supplies , and if not found in supplies make with default (mycard)
        var Component = lazy(() => import("@components/supplies/" + name));
        var el = <Suspense fallback={<div>Hi, This tile is Loading...</div>}>
            <Component item={elements} canSee={this.purchaseOrder.canSee} />
        </Suspense>;

        return el;
    }

    makeTile(element, layout) {
        // merge objects with an interface of tile needed
        // var tileValues = {
        //     id: element.id,
        //     counted: element.counted,//"2",
        //     type: element.type, //"Card"
        //     title: element.title,
        //     text: element.description,
        //     path: element.path,
        //     linkLabel: element.linkLabel,
        // };

        var item = {
            ...element,
            ...this.makeCoordinates(element, layout)
        };

        var tile = <div key={item.type + item.id} data-grid={item}>
            {this.getComponent(item.type, item)}
        </div>;
        return tile;
        // return tile;
    }

    randomize(element) {
        var model = tileModels.find(o => o.type === element.type);
        var p = 2;

        return {
            w: model.minw,//largeur
            h: model.minh,//hauteur
            x: Math.floor(p / 6) * element.id + model.x ?? 0, //position en x
            y: Math.floor(p + 2 / 6) * model.y ?? 1 + model.y ?? 0, //position en y
            i: element.type + element.id.toString() //key name
        };
    }

    makeCoordinates(element, layout) {
        if (typeof layout !== 'object') {
            //if not in layout random coordinates and default values according to tile model
            return this.randomize(element);
        } else {
            //else size with layout and tile model
            return layout;
        }
    }

}
