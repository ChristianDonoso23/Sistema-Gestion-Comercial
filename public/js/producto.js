const createProductPanel = () => {
    Ext.define("App.model.Product", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "nombre", type: "string" },
            { name: "descripcion", type: "string" },
            { name: "precioUnitario", type: "float" },
            { name: "stock", type: "int" },
            { name: "idCategoria", type: "int" }
        ]
    });

    let productStore = Ext.create("Ext.data.Store", {
        storeId: "productStore",
        model: "App.model.Product",
        proxy: {
            type: "rest",
            url: "/Api/producto.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: 'json', rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Productos",
        store: productStore,
        itemId: "productPanel",
        layout: "fit",
        columns: [
            {
                text: "ID",
                width: 40,
                sortable: false,
                hideable: false,
                dataIndex: "id",
            },
            {
                text: "Nombre",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "nombre",
            },
            {
                text: "Descripción",
                flex: 2,
                sortable: false,
                hideable: false,
                dataIndex: "descripcion",
            },
            {
                text: "Precio Unitario",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "precioUnitario",
                renderer: Ext.util.Format.usMoney
            },
            {
                text: "Stock",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "stock",
            },
            {
                text: "ID Categoría",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "idCategoria",
            }
        ],
    });

    return grid;
};
