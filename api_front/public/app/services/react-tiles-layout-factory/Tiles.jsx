import React from "react";
import _ from "lodash";
import RGL, { WidthProvider } from "react-grid-layout";
import TileFactory from "./TileFactory";
import PropTypes from 'prop-types';
import localStore from "@services/localStore";
const ReactGridLayout = WidthProvider(RGL);

export default class Tiles extends React.PureComponent {
    static propTypes = {
        items: PropTypes.array,
        page: PropTypes.string,
        canSee: PropTypes.func,
        onLayoutChange: PropTypes.func
    }

    static defaultProps = {
        isDraggable: true,
        isResizable: true,
        rowHeight: 10,
        onLayoutChange: function () { },
        cols: 12
    };

    constructor(props) {
        super(props);
        const page = props.page;
        const originalLayout = localStore.getLs("layout", page) || [];

        this.state = {
            layout: JSON.parse(JSON.stringify(originalLayout)),
            page: page
        };

        this.onLayoutChange = this.onLayoutChange.bind(this);
        this.resetLayout = this.resetLayout.bind(this);
    }

    resetLayout() {
        this.setState({
            layout: []
        });
    }

    onLayoutChange(layout) {
        /*eslint no-console: 0*/
        if (layout.length > 0) {
            this.setState({ layout });
        }
    }

    // componentDidMount() {
    // }

    componentDidUpdate() {
        localStore.setLs("layout", this.state.page, this.state.layout);
        this.setState({ layout: this.state.layout });
    }

    // componentWillUnmount() {}

    generateDOM(items) {
        if (items && items.length > 0) {
            const johnFactory = new TileFactory({ canSee: this.props.canSee });
            return _.map(items, element => {
                var layout = {};
                if (element.layout) {
                    layout = element.layout;
                } else {
                    layout = this.state.layout.find(o => o.i === element.type + element.id.toString());
                }
                var tile = johnFactory.makeTile(element, layout);

                return tile;
            });
        } else {
            return [];
        }
    }

    render() {
        const items = this.props.items;
        return (
            <div>
                {/* laisser en dev mais commenter en prod */}
                {/* <button className="btn btn-outline-secondary" onClick={this.resetLayout}>Reset Layout</button> */}
                <ReactGridLayout
                    {...this.props}
                    layout={this.state.layout}
                    onLayoutChange={this.onLayoutChange}
                >
                    {this.generateDOM(items)}

                </ReactGridLayout>
            </div>
        );
    }
}
