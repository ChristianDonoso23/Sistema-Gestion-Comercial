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
            url: "/Api/factura.php",
            reader: { type: "json", rootProperty: "" },
            writer: { type: "json", rootProperty: "", writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Facturas",
        store: facturaStore,
        itemId: "facturaPanel",
        layout: "fit",
        columns: [
            { 
                text: "ID", 
                width: 50, 
                sortable: false, 
                hideable: false, 
                dataIndex: "id" 
            },
            { 
                text: "ID Venta", 
                width: 80, 
                sortable: false, 
                hideable: false, 
                dataIndex: "idVenta" 
            },
            { 
                text: "Número", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "numero" 
            },
            { 
                text: "Clave de Acceso", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "claveAcceso" 
            },
            { 
                text: "Fecha Emisión", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "fechaEmision", 
                xtype: "datecolumn", 
                format: "Y-m-d" 
            },
            { 
                text: "Estado", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "estado" 
            }
        ]
    });

    return grid;
};
