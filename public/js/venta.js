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
            reader: { type: "json", rootProperty: "" },
            writer: { type: "json", rootProperty: "", writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Ventas",
        store: salesStore,
        itemId: "salesPanel",
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
                text: "Fecha",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "fecha",
                xtype: "datecolumn",
                format: "Y-m-d"
            },
            {
                text: "ID Cliente",
                width: 100,
                sortable: false,
                hideable: false,
                dataIndex: "idCliente"
            },
            {
                text: "Total",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "total",
                xtype: "numbercolumn",
                format: "0.00"
            },
            {
                text: "Estado",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "estado"
            }
        ],
        tbar: [
            {
                text: 'Eliminar Venta',
                handler() {
                    const rec = this.up('grid').getSelection()[0];
                    if (!rec) return Ext.Msg.alert('Atención', 'Seleccione una venta para eliminar.'); 

                    Ext.Msg.confirm('Confirmar', '¿Está seguro de que desea eliminar esta venta?', btn => {
                        if (btn === 'yes') {
                            salesStore.remove(rec);
                            salesStore.sync({
                                success: () => Ext.Msg.alert('Éxito', 'Venta eliminada correctamente.'), 
                                failure: () => Ext.Msg.alert('Error', 'No se pudo eliminar la venta.') 
                            });
                        }
                    });
                }
            }
        ]

    });

    return grid;
};
