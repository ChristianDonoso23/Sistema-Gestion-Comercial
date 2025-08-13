const createSalesPanel = () => {
    Ext.define("App.model.Venta", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "fecha", type: "date", dateFormat: "Y-m-d" },
            { name: "idCliente", type: "int" },
            { name: "total", type: "float" },
            { name: "estado", type: "string" } 
        ]
    });

    let salesStore = Ext.create("Ext.data.Store", {
        storeId: "salesStore",
        model: "App.model.Venta",
        proxy: {
            type: "rest",
            url: "/Api/venta.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: 'json', rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Ventas",
        store: salesStore,
        itemId: "salesPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 50, dataIndex: "id" },
            { text: "Fecha", dataIndex: "fecha", flex: 1, xtype: 'datecolumn', format: 'Y-m-d' },
            { text: "IDcliente", width: 100, dataIndex: "idCliente" },
            { text: "Total", flex: 1, dataIndex: "total", xtype: "numbercolumn", format: "0.00" },
            { text: "Estado", flex: 1, dataIndex: "estado" }
        ]
    });

    return grid;
};
