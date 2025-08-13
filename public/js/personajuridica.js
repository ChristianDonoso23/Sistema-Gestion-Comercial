const createLegalPersonPanel = () => {
    Ext.define("App.model.PersonaJuridica", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "email", type: "string" },
            { name: "telefono", type: "string" },
            { name: "direccion", type: "string" },
            { name: "razonSocial", type: "string" },
            { name: "ruc", type: "string" },
            { name: "representanteLegal", type: "string" }
        ]
    });

    let personaJuridicaStore = Ext.create("Ext.data.Store", {
        storeId: "personaJuridicaStore",
        model: "App.model.PersonaJuridica",
        proxy: {
            type: "rest",
            url: "/api/personajuridica.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: "json", rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Persona Jurídica",
        store: personaJuridicaStore,
        itemId: "personaJuridicaPanel",
        layout: "fit",
        columns: [
            { text: "ID", dataIndex: "id", width: 50 },
            { text: "Email", dataIndex: "email", flex: 1 },
            { text: "Teléfono", dataIndex: "telefono", flex: 1 },
            { text: "Dirección", dataIndex: "direccion", flex: 1 },
            { text: "Razón Social", dataIndex: "razonSocial", flex: 1 },
            { text: "RUC", dataIndex: "ruc", flex: 1 },
            { text: "Representante Legal", dataIndex: "representanteLegal", flex: 1 }
        ],
    });

    return grid;
};
