const createBillPanel = () => {
    Ext.define("App.model.Factura", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "idVenta", type: "int" },
            { name: "numero", type: "string" },
            { name: "claveAcceso", type: "string" },
            { name: "fechaEmision", type: "date", dateFormat: "Y-m-d" },
            { name: "estado", type: "string" }
        ]
    });

    let facturaStore = Ext.create("Ext.data.Store", {
        storeId: "facturaStore",
        model: "App.model.Factura",
        proxy: {
            type: "rest",
            url: "/api/factura.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: "json", rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Facturas",
        store: facturaStore,
        itemId: "facturaPanel",
        layout: "fit",
        columns: [
            { text: "ID", dataIndex: "id", width: 50 },
            { text: "ID Venta", dataIndex: "idVenta", width: 70 },
            { text: "Número", dataIndex: "numero", flex: 1 },
            { text: "Clave de Acceso", dataIndex: "claveAcceso", flex: 2 },
            { text: "Fecha Emisión", dataIndex: "fechaEmision", flex: 1, xtype: 'datecolumn', format: 'Y-m-d' },
            { text: "Estado", dataIndex: "estado", flex: 1 }
        ],
    });

    return grid;
};
