const createPermissionPanel = () => {
    Ext.define("App.model.Permiso", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "codigo", type: "string" }
        ]
    });

    let permisoStore = Ext.create("Ext.data.Store", {
        storeId: "permisoStore",
        model: "App.model.Permiso",
        proxy: {
            type: "rest",
            url: "/Api/permiso.php",
            reader: { type: "json", rootProperty: "" },
            writer: { type: "json", rootProperty: "", writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Permisos",
        store: permisoStore,
        itemId: "permisoPanel",
        layout: "fit",
        columns: [
            { 
                text: "ID", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "id", 
                align: "center" 
            },
            { 
                text: "Código", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "codigo", 
                align: "center" 
            }
        ]
    });

    return grid;
};
