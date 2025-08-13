const createDetailSalesPanel = () => {
    Ext.define("App.model.DetalleVenta", {
        extend: "Ext.data.Model",
        fields: [
            { name: "idVenta", type: "int" },
            { name: "lineNumber", type: "int" },
            { name: "idProducto", type: "int" },
            { name: "cantidad", type: "int" },
            { name: "precioUnitario", type: "float" },
            { name: "subtotal", type: "float" }
        ]
    });

    let detailSalesStore = Ext.create("Ext.data.Store", {
        storeId: "detailSalesStore",
        model: "App.model.DetalleVenta",
        proxy: {
            type: "rest",
            url: "/api/detalleventa.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: 'json', rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Detalle de Ventas",
        store: detailSalesStore,
        itemId: "detailSalesPanel",
        layout: "fit",
        columns: [
            { text: "ID Venta", width: 70, dataIndex: "idVenta" },
            { text: "Linea", width: 60, dataIndex: "lineNumber" },
            { text: "ID Producto", flex: 1, dataIndex: "idProducto" },
            { text: "Cantidad", flex: 1, dataIndex: "cantidad" },
            { text: "Precio Unitario", flex: 1, dataIndex: "precioUnitario", xtype: "numbercolumn", format: "0.00" },
            { text: "Subtotal", flex: 1, dataIndex: "subtotal", xtype: "numbercolumn", format: "0.00" }
        ]
    });

    return grid;
};
